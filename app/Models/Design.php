<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    use HasFactory;

    protected $primaryKey = 'design_id';

    protected $fillable = [
        'order_id',
        'designer_id',
        'design_files',
        'design_notes',
        'status',
        'version',
        'feedback',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
    ];

    protected $casts = [
        'design_files' => 'array',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Get the order that owns the design
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /**
     * Get the designer who created the design
     */
    public function designer()
    {
        return $this->belongsTo(User::class, 'designer_id');
    }

    /**
     * Get the user who approved the design
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who rejected the design
     */
    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Check if design is pending review
     */
    public function isPendingReview()
    {
        return $this->status === 'Pending Review';
    }

    /**
     * Check if design is approved
     */
    public function isApproved()
    {
        return $this->status === 'Approved';
    }

    /**
     * Check if design is rejected
     */
    public function isRejected()
    {
        return $this->status === 'Rejected';
    }

    /**
     * Get design files as array
     */
    public function getDesignFilesArray()
    {
        return is_array($this->design_files) ? $this->design_files : (json_decode($this->design_files, true) ?: []);
    }
} 