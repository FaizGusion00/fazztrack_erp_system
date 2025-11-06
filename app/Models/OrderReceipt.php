<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    /**
     * Get the order that owns the receipt
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /**
     * Get the full URL for the receipt file
     */
    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }
}

