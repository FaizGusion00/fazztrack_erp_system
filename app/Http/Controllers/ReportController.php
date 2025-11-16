<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Job;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // Only SuperAdmin can access reports
        if (!$user->isSuperAdmin()) {
            abort(403, 'Access denied. Only SuperAdmin can access reports.');
        }

        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Set default date range if not provided
        if (!$startDate || !$endDate) {
            $endDate = Carbon::now()->format('Y-m-d');
            switch ($period) {
                case 'week':
                    $startDate = Carbon::now()->subWeek()->format('Y-m-d');
                    break;
                case 'month':
                    $startDate = Carbon::now()->subMonth()->format('Y-m-d');
                    break;
                case 'quarter':
                    $startDate = Carbon::now()->subQuarter()->format('Y-m-d');
                    break;
                case 'year':
                    $startDate = Carbon::now()->subYear()->format('Y-m-d');
                    break;
                default:
                    $startDate = Carbon::now()->subMonth()->format('Y-m-d');
            }
        }

        // Get comprehensive statistics
        $stats = $this->getComprehensiveStats($startDate, $endDate);
        
        // Get chart data
        $chartData = $this->getChartData($startDate, $endDate);
        
        // Sorting functionality (for any list data in reports)
        $sort = $request->get('sort', 'latest_added');

        return view('reports.index', compact('stats', 'chartData', 'startDate', 'endDate', 'period', 'sort'));
    }

    /**
     * Get comprehensive statistics
     */
    private function getComprehensiveStats($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        return [
            // Order Statistics
            'total_orders' => Order::whereBetween('created_at', [$start, $end])->count(),
            'completed_orders' => Order::whereBetween('created_at', [$start, $end])->where('status', 'Completed')->count(),
            'pending_orders' => Order::whereBetween('created_at', [$start, $end])->where('status', 'Pending')->count(),
            'total_revenue' => Order::whereBetween('created_at', [$start, $end])->sum(DB::raw('design_deposit + production_deposit + balance_payment')),
            'average_order_value' => Order::whereBetween('created_at', [$start, $end])->avg(DB::raw('design_deposit + production_deposit + balance_payment')),
            
            // Job Statistics
            'total_jobs' => Job::whereBetween('created_at', [$start, $end])->count(),
            'completed_jobs' => Job::whereBetween('created_at', [$start, $end])->where('status', 'Completed')->count(),
            'pending_jobs' => Job::whereBetween('created_at', [$start, $end])->where('status', 'Pending')->count(),
            'average_job_duration' => Job::whereBetween('created_at', [$start, $end])->whereNotNull('duration')->avg('duration'),
            
            // User Statistics
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'users_by_role' => User::select('role', DB::raw('count(*) as count'))->groupBy('role')->get(),
            
            // Client Statistics
            'total_clients' => Client::whereBetween('created_at', [$start, $end])->count(),
            'new_clients' => Client::whereBetween('created_at', [$start, $end])->count(),
            'top_clients' => Client::withCount(['orders' => function($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end]);
            }])->orderBy('orders_count', 'desc')->take(5)->get(),
            
            // Product Statistics
            'total_products' => Product::count(),
            'active_products' => Product::where('status', 'Active')->count(),
            'low_stock_products' => Product::where('stock', '<', 10)->count(),
            'out_of_stock_products' => Product::where('stock', 0)->count(),
            
            // Production Statistics
            'production_efficiency' => $this->calculateProductionEfficiency($start, $end),
            'phase_completion_times' => $this->getPhaseCompletionTimes($start, $end),
        ];
    }

    /**
     * Get chart data
     */
    private function getChartData($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        return [
            // Revenue over time
            'revenue_chart' => Order::selectRaw('DATE(created_at) as date, SUM(design_deposit + production_deposit + balance_payment) as revenue')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('date')
                ->orderBy('date')
                ->get(),

            // Orders over time
            'orders_chart' => Order::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('date')
                ->orderBy('date')
                ->get(),

            // Jobs by phase
            'jobs_by_phase' => Job::selectRaw('phase, COUNT(*) as count')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('phase')
                ->get(),

            // Orders by status
            'orders_by_status' => Order::selectRaw('status, COUNT(*) as count')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('status')
                ->get(),

            // User activity
            'user_activity' => User::selectRaw('role, COUNT(*) as count')
                ->groupBy('role')
                ->get(),
        ];
    }

    /**
     * Calculate production efficiency
     */
    private function calculateProductionEfficiency($start, $end)
    {
        $totalJobs = Job::whereBetween('created_at', [$start, $end])->count();
        $completedJobs = Job::whereBetween('created_at', [$start, $end])->where('status', 'Completed')->count();
        
        return $totalJobs > 0 ? round(($completedJobs / $totalJobs) * 100, 2) : 0;
    }

    /**
     * Get phase completion times
     */
    private function getPhaseCompletionTimes($start, $end)
    {
        return Job::selectRaw('phase, AVG(duration) as avg_duration, COUNT(*) as total_jobs')
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('duration')
            ->groupBy('phase')
            ->get();
    }

    /**
     * Generate detailed order report
     */
    public function orderReport(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Access denied. Only SuperAdmin can access reports.');
        }

        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $orders = Order::with(['client', 'product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reports.order-report', compact('orders', 'startDate', 'endDate'));
    }

    /**
     * Generate production report
     */
    public function productionReport(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Access denied. Only SuperAdmin can access reports.');
        }

        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $jobs = Job::with(['order.client', 'assignedUser'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $phaseStats = Job::selectRaw('phase, COUNT(*) as total, 
            SUM(CASE WHEN status = "Completed" THEN 1 ELSE 0 END) as completed,
            AVG(duration) as avg_duration')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('phase')
            ->get();

        return view('reports.production-report', compact('jobs', 'phaseStats', 'startDate', 'endDate'));
    }

    /**
     * Generate user performance report
     */
    public function userReport(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Access denied. Only SuperAdmin can access reports.');
        }

        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $users = User::withCount(['assignedJobs' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
        ->withSum(['assignedJobs' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }], 'duration')
        ->orderBy('assigned_jobs_count', 'desc')
        ->get();

        return view('reports.user-report', compact('users', 'startDate', 'endDate'));
    }

    /**
     * Generate financial report
     */
    public function financialReport(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Access denied. Only SuperAdmin can access reports.');
        }

        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $orders = Order::whereBetween('created_at', [$startDate, $endDate])->get();

        $financialStats = [
            'total_revenue' => $orders->sum(function($order) {
                return $order->design_deposit + $order->production_deposit + $order->balance_payment;
            }),
            'design_deposits' => $orders->sum('design_deposit'),
            'production_deposits' => $orders->sum('production_deposit'),
            'balance_payments' => $orders->sum('balance_payment'),
            'average_order_value' => $orders->avg(function($order) {
                return $order->design_deposit + $order->production_deposit + $order->balance_payment;
            }),
            'revenue_by_month' => Order::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, 
                SUM(design_deposit + production_deposit + balance_payment) as revenue')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get(),
        ];

        return view('reports.financial-report', compact('financialStats', 'startDate', 'endDate'));
    }

    /**
     * Export report data
     */
    public function export(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Access denied. Only SuperAdmin can export reports.');
        }

        $reportType = $request->get('type', 'orders');
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $filename = $reportType . '_report_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($reportType, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            switch ($reportType) {
                case 'orders':
                    $this->exportOrders($file, $startDate, $endDate);
                    break;
                case 'clients':
                    $this->exportClients($file, $startDate, $endDate);
                    break;
                case 'jobs':
                    $this->exportJobs($file, $startDate, $endDate);
                    break;
                case 'production':
                    $this->exportProduction($file, $startDate, $endDate);
                    break;
                case 'users':
                    $this->exportUsers($file, $startDate, $endDate);
                    break;
                case 'financial':
                    $this->exportFinancial($file, $startDate, $endDate);
                    break;
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export orders data
     */
    private function exportOrders($file, $startDate, $endDate)
    {
        fputcsv($file, ['Order ID', 'Client', 'Job Name', 'Status', 'Total Amount', 'Created Date']);
        
        $orders = Order::with('client')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
            
        foreach ($orders as $order) {
            $totalAmount = $order->design_deposit + $order->production_deposit + $order->balance_payment;
            fputcsv($file, [
                $order->order_id,
                $order->client->name,
                $order->job_name,
                $order->status,
                $totalAmount,
                $order->created_at->format('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Export production data
     */
    private function exportProduction($file, $startDate, $endDate)
    {
        fputcsv($file, ['Job ID', 'Order ID', 'Phase', 'Status', 'Duration (min)', 'Assigned User', 'Created Date']);
        
        $jobs = Job::with(['order', 'assignedUser'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
            
        foreach ($jobs as $job) {
            fputcsv($file, [
                $job->id,
                $job->order->order_id,
                $job->phase,
                $job->status,
                $job->duration ?? 'N/A',
                $job->assignedUser->name ?? 'Unassigned',
                $job->created_at->format('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Export users data
     */
    private function exportUsers($file, $startDate, $endDate)
    {
        fputcsv($file, ['User ID', 'Name', 'Role', 'Jobs Assigned', 'Total Duration', 'Status']);
        
        $users = User::withCount(['assignedJobs' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
        ->withSum(['assignedJobs' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }], 'duration')
        ->get();
            
        foreach ($users as $user) {
            fputcsv($file, [
                $user->id,
                $user->name,
                $user->role,
                $user->assigned_jobs_count,
                $user->assigned_jobs_sum_duration ?? 0,
                $user->is_active ? 'Active' : 'Inactive',
            ]);
        }
    }

    /**
     * Export financial data
     */
    private function exportFinancial($file, $startDate, $endDate)
    {
        fputcsv($file, ['Date', 'Revenue', 'Orders Count', 'Average Order Value']);
        
        $revenueData = Order::selectRaw('DATE(created_at) as date, 
            SUM(design_deposit + production_deposit + balance_payment) as revenue,
            COUNT(*) as orders_count,
            AVG(design_deposit + production_deposit + balance_payment) as avg_order_value')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        foreach ($revenueData as $data) {
            fputcsv($file, [
                $data->date,
                $data->revenue,
                $data->orders_count,
                $data->avg_order_value,
            ]);
        }
    }

    private function exportClients($file, $startDate, $endDate)
    {
        fputcsv($file, ['Client ID', 'Name', 'Email', 'Phone', 'Customer Type', 'Created Date']);
        $clients = Client::whereBetween('created_at', [$startDate, $endDate])->get();
        foreach ($clients as $client) {
            fputcsv($file, [
                $client->client_id,
                $client->name,
                $client->email,
                $client->phone,
                $client->customer_type,
                $client->created_at->format('Y-m-d H:i:s'),
            ]);
        }
    }

    private function exportJobs($file, $startDate, $endDate)
    {
        fputcsv($file, ['Job ID', 'Order ID', 'Phase', 'Status', 'Duration (min)', 'Assigned User', 'Created Date']);
        $jobs = Job::with(['order', 'assignedUser'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        foreach ($jobs as $job) {
            fputcsv($file, [
                $job->id,
                $job->order->order_id ?? '',
                $job->phase,
                $job->status,
                $job->duration ?? 'N/A',
                $job->assignedUser->name ?? 'Unassigned',
                $job->created_at->format('Y-m-d H:i:s'),
            ]);
        }
    }
} 