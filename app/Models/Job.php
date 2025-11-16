<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Job extends Model
{
    protected $table = 'production_jobs';
    use HasFactory;

    protected $primaryKey = 'job_id';

    protected $fillable = [
        'order_id',
        'phase',
        'status',
        'qr_code',
        'start_time',
        'end_time',
        'duration',
        'remarks',
        'start_quantity',
        'end_quantity',
        'reject_quantity',
        'reject_status',
        'assigned_user_id',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration' => 'integer',
        'start_quantity' => 'integer',
        'end_quantity' => 'integer',
        'reject_quantity' => 'integer',
        'phase' => 'string',
        'status' => 'string',
    ];

    /**
     * Get the order that owns the job.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /**
     * Get the assigned user for the job.
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id', 'id');
    }

    /**
     * Generate QR code for the job
     */
    public function generateQrCode()
    {
        $qrData = json_encode([
            'job_id' => $this->job_id,
            'order_id' => $this->order_id,
            'phase' => $this->phase,
            'qr_code' => $this->qr_code,
        ]);

        return QrCode::size(200)->generate($qrData);
    }

    /**
     * Start the job (capture start time)
     */
    public function startJob($startQuantity = null)
    {
        $this->update([
            'status' => 'In Progress',
            'start_time' => now(),
            'start_quantity' => $startQuantity,
        ]);
    }

    /**
     * End the job (capture end time and calculate duration)
     */
    public function endJob($endQuantity = null, $rejectQuantity = null, $rejectStatus = null, $remarks = null)
    {
        $endTime = now();
        $duration = null;

        if ($this->start_time) {
            // Calculate duration as positive value (end_time - start_time)
            $duration = $this->start_time->diffInMinutes($endTime, false);
        }

        $this->update([
            'status' => 'Completed',
            'end_time' => $endTime,
            'duration' => $duration,
            'end_quantity' => $endQuantity,
            'reject_quantity' => $rejectQuantity,
            'reject_status' => $rejectStatus,
            'remarks' => $remarks,
        ]);
    }

    /**
     * Check if job only captures start time (Draft, Shipping, Complete phases)
     */
    public function isStartTimeOnly()
    {
        return in_array($this->phase, ['Draft', 'Shipping', 'Complete']);
    }

    /**
     * Get the next job in sequence
     */
    public function getNextJob()
    {
        $phases = ['PRINT', 'PRESS', 'CUT', 'SEW', 'QC'];
        $currentIndex = array_search($this->phase, $phases);
        
        if ($currentIndex !== false && $currentIndex < count($phases) - 1) {
            return $this->order->jobs()->where('phase', $phases[$currentIndex + 1])->first();
        }
        
        return null;
    }
} 