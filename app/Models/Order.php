<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';

    protected $fillable = [
        'client_id',
        'job_name',
        'delivery_method',
        'status',
        'status_comment',
        'design_deposit',
        'production_deposit',
        'balance_payment',
        'total_amount',
        'due_date_design',
        'due_date_production',
        'remarks',
        'receipts',
        'job_sheet',
        'design_files',
        'download_link',
    ];

    protected $casts = [
        'design_deposit' => 'decimal:2',
        'production_deposit' => 'decimal:2',
        'balance_payment' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'due_date_design' => 'date',
        'due_date_production' => 'date',
        'delivery_method' => 'string',
        'status' => 'string',
        'design_files' => 'array',
    ];

    /**
     * Boot method to automatically calculate total_amount
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($order) {
            $order->total_amount = $order->design_deposit + $order->production_deposit + $order->balance_payment;
        });
    }

    /**
     * Get the client that owns the order.
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    /**
     * Get the jobs for the order.
     */
    public function jobs()
    {
        return $this->hasMany(Job::class, 'order_id', 'order_id');
    }

    public function designs()
    {
        return $this->hasMany(Design::class, 'order_id', 'order_id');
    }

    /**
     * Check if order is overdue
     */
    public function isOverdue()
    {
        return $this->due_date_production < now();
    }

    /**
     * Check if order is due within 7 days
     */
    public function isDueSoon()
    {
        return $this->due_date_production->diffInDays(now()) <= 7;
    }

    /**
     * Get design files array
     */
    public function getDesignFilesArray()
    {
        return is_array($this->design_files) ? $this->design_files : (json_decode($this->design_files, true) ?: []);
    }

    /**
     * Accessor for design_front
     */
    public function getDesignFrontAttribute()
    {
        $designFiles = $this->getDesignFilesArray();
        return $designFiles['design_front'] ?? null;
    }

    /**
     * Accessor for design_back
     */
    public function getDesignBackAttribute()
    {
        $designFiles = $this->getDesignFilesArray();
        return $designFiles['design_back'] ?? null;
    }

    /**
     * Accessor for design_left
     */
    public function getDesignLeftAttribute()
    {
        $designFiles = $this->getDesignFilesArray();
        return $designFiles['design_left'] ?? null;
    }

    /**
     * Accessor for design_right
     */
    public function getDesignRightAttribute()
    {
        $designFiles = $this->getDesignFilesArray();
        return $designFiles['design_right'] ?? null;
    }
} 