<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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
        'status_before_hold',
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
        'pola_link',
        'delivery_status',
        'tracking_number',
        'delivery_date',
        'delivery_notes',
        'delivery_company',
        'payment_status',
        'paid_amount',
        'last_payment_date',
        'payment_notes',
        'payment_due_date',
        'proof_of_delivery_path',
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
        'delivery_date' => 'datetime',
        'last_payment_date' => 'datetime',
        'payment_due_date' => 'date',
        'paid_amount' => 'decimal:2',
        'status_before_hold' => 'string',
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
     * Get the product for this order (legacy - for backward compatibility).
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Get all products for this order.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products', 'order_id', 'product_id')
                    ->withPivot('quantity', 'comments')
                    ->withTimestamps();
    }

    /**
     * Get the order products with pivot data.
     */
    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'order_id');
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
     * Get the receipts for the order
     */
    public function receipts()
    {
        return $this->hasMany(OrderReceipt::class, 'order_id', 'order_id');
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class, 'order_id', 'order_id')->latest();
    }

    /**
     * Check if order is overdue
     */
    public function isOverdue()
    {
        $dueDate = $this->due_date_production;
        return $dueDate instanceof Carbon ? $dueDate->lt(now()) : false;
    }

    /**
     * Check if order is due within 7 days
     */
    public function isDueSoon()
    {
        $dueDate = $this->due_date_production;
        return $dueDate instanceof Carbon ? $dueDate->diffInDays(now()) <= 7 : false;
    }

    /**
     * Get design files array
     */
    public function getDesignFilesArray()
    {
        return is_array($this->design_files) ? $this->design_files : [];
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