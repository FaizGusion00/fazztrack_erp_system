<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\OfflineJobLog;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OfflineController extends Controller
{
    /**
     * Get jobs for offline mode
     */
    public function getOfflineJobs()
    {
        $user = Auth::user();
        
        if (!$user->isProductionStaff()) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        // STRICT WORKFLOW: Production staff can only see jobs from orders in production phase
        // Order status must be "Job Start" or higher
        $productionAllowedStatuses = ['Job Start', 'Job Complete', 'Order Packaging', 'Order Finished', 'Completed'];
        
        // Get all active jobs that production staff can work on
        $jobs = Job::with(['order.client'])
            ->excludeOnHoldOrders()
            ->where('status', '!=', 'Completed')
            ->whereHas('order', function($query) use ($productionAllowedStatuses) {
                $query->whereIn('status', $productionAllowedStatuses);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($job) {
                return [
                    'id' => $job->id,
                    'qr_code' => $job->qr_code,
                    'phase' => $job->phase,
                    'status' => $job->status,
                    'order_id' => $job->order_id,
                    'order_number' => $job->order->order_number,
                    'client_name' => $job->order->client->name,
                    'description' => $job->description,
                    'start_time' => $job->start_time,
                    'end_time' => $job->end_time,
                    'duration' => $job->duration,
                    'notes' => $job->notes,
                    'created_at' => $job->created_at,
                    'updated_at' => $job->updated_at
                ];
            });

        return response()->json([
            'jobs' => $jobs,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Log offline job action
     */
    public function logOfflineAction(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isProductionStaff()) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'action' => 'required|in:start,end,pause,resume',
            'action_time' => 'required|date',
            'notes' => 'nullable|string',
            'offline_data' => 'nullable|array'
        ]);

        $offlineLog = OfflineJobLog::create([
            'job_id' => $request->job_id,
            'user_id' => $user->id,
            'action' => $request->action,
            'action_time' => $request->action_time,
            'notes' => $request->notes,
            'offline_data' => $request->offline_data,
            'synced' => false
        ]);

        return response()->json([
            'success' => true,
            'log_id' => $offlineLog->id,
            'message' => 'Offline action logged successfully'
        ]);
    }

    /**
     * Sync offline logs when back online
     */
    public function syncOfflineLogs(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isProductionStaff()) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $request->validate([
            'logs' => 'required|array',
            'logs.*.job_id' => 'required|exists:jobs,id',
            'logs.*.action' => 'required|in:start,end,pause,resume',
            'logs.*.action_time' => 'required|date',
            'logs.*.notes' => 'nullable|string',
            'logs.*.offline_data' => 'nullable|array'
        ]);

        $syncedCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($request->logs as $logData) {
                // Create offline log
                $offlineLog = OfflineJobLog::create([
                    'job_id' => $logData['job_id'],
                    'user_id' => $user->id,
                    'action' => $logData['action'],
                    'action_time' => $logData['action_time'],
                    'notes' => $logData['notes'] ?? null,
                    'offline_data' => $logData['offline_data'] ?? null,
                    'synced' => true,
                    'synced_at' => now()
                ]);

                // Update job based on action
                $job = Job::with('order')->find($logData['job_id']);

                if ($job && $job->isOrderOnHold()) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'One of the jobs you attempted to sync belongs to an order that is currently on hold. Resume the order before syncing.',
                        'job_id' => $job->job_id,
                    ], 423);
                }
                
                switch ($logData['action']) {
                    case 'start':
                        if ($job->status === 'Pending') {
                            $job->update([
                                'status' => 'In Progress',
                                'start_time' => $logData['action_time'],
                                'notes' => $logData['notes'] ?? $job->notes
                            ]);
                            
                            // Update order status if this is the first job started
                            $order = $job->order;
                            if ($order->status === 'Job Created') {
                                $order->update(['status' => 'Job Start']);
                            }
                        }
                        break;
                        
                    case 'end':
                        if ($job->status === 'In Progress') {
                            $endTime = \Carbon\Carbon::parse($logData['action_time']);
                            $startTime = $job->start_time ? \Carbon\Carbon::parse($job->start_time) : null;
                            $duration = $startTime ? $startTime->diffInMinutes($endTime) : null;
                            
                            $job->update([
                                'status' => 'Completed',
                                'end_time' => $logData['action_time'],
                                'duration' => $duration,
                                'notes' => $logData['notes'] ?? $job->notes
                            ]);
                            
                            // Update order status based on job completion
                            $this->updateOrderStatusAfterJobCompletion($job);
                        }
                        break;
                        
                    case 'pause':
                        if ($job->status === 'In Progress') {
                            $job->update([
                                'status' => 'Paused',
                                'notes' => $logData['notes'] ?? $job->notes
                            ]);
                        }
                        break;
                        
                    case 'resume':
                        if ($job->status === 'Paused') {
                            $job->update([
                                'status' => 'In Progress',
                                'notes' => $logData['notes'] ?? $job->notes
                            ]);
                        }
                        break;
                }
                
                $syncedCount++;
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'synced_count' => $syncedCount,
                'message' => "Successfully synced {$syncedCount} offline actions"
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Sync failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unsynced logs for a user
     */
    public function getUnsyncedLogs()
    {
        $user = Auth::user();
        
        if (!$user->isProductionStaff()) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $logs = OfflineJobLog::with(['job.order.client'])
            ->where('user_id', $user->id)
            ->where('synced', false)
            ->orderBy('action_time', 'asc')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'job_id' => $log->job_id,
                    'action' => $log->action,
                    'action_time' => $log->action_time->toISOString(),
                    'notes' => $log->notes,
                    'offline_data' => $log->offline_data,
                    'job' => [
                        'id' => $log->job->id,
                        'phase' => $log->job->phase,
                        'status' => $log->job->status,
                        'order_number' => $log->job->order->order_number,
                        'client_name' => $log->job->order->client->name
                    ]
                ];
            });

        return response()->json([
            'logs' => $logs,
            'count' => $logs->count()
        ]);
    }

    /**
     * Check online status and sync if needed
     */
    public function checkOnlineStatus()
    {
        $user = Auth::user();
        
        if (!$user->isProductionStaff()) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $unsyncedCount = OfflineJobLog::where('user_id', $user->id)
            ->where('synced', false)
            ->count();

        return response()->json([
            'online' => true,
            'unsynced_count' => $unsyncedCount,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Update order status after job completion
     */
    private function updateOrderStatusAfterJobCompletion($completedJob)
    {
        $order = $completedJob->order;
        $allJobs = $order->jobs;
        
        // Check if all jobs are completed
        $allCompleted = $allJobs->every(function ($job) {
            return $job->status === 'Completed';
        });
        
        if ($allCompleted) {
            $order->update(['status' => 'Order Finished']);
        } else {
            // Check if this was a QC job completion
            if ($completedJob->phase === 'QC' && $completedJob->status === 'Completed') {
                // QC phase now handles packing, so when QC completes, order is finished
                $order->update(['status' => 'Order Finished']);
            }
        }
    }
}
