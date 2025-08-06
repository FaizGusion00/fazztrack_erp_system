<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'name',
        'description',
        'size',
        'price',
        'stock',
        'images',
        'comments',
        'status',
        'category',
        'color',
        'material',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'images' => 'array',
        'status' => 'string',
    ];

    /**
     * Get the orders that use this product.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'product_id', 'product_id');
    }

    /**
     * Get the first product image URL
     */
    public function getFirstImageUrlAttribute()
    {
        if ($this->images && count($this->images) > 0) {
            return Storage::url($this->images[0]);
        }
        return null;
    }

    /**
     * Get all product image URLs
     */
    public function getImageUrlsAttribute()
    {
        if (!$this->images) {
            return [];
        }
        
        return collect($this->images)->map(function ($image) {
            return Storage::url($image);
        })->toArray();
    }

    /**
     * Check if product is in stock
     */
    public function isInStock()
    {
        return $this->stock > 0;
    }

    /**
     * Check if product is low in stock (less than 10)
     */
    public function isLowStock()
    {
        return $this->stock > 0 && $this->stock < 10;
    }

    /**
     * Get stock status text
     */
    public function getStockStatusAttribute()
    {
        if ($this->stock <= 0) {
            return 'Out of Stock';
        } elseif ($this->stock < 10) {
            return 'Low Stock';
        } else {
            return 'In Stock';
        }
    }

    /**
     * Get stock status color
     */
    public function getStockStatusColorAttribute()
    {
        if ($this->stock <= 0) {
            return 'red';
        } elseif ($this->stock < 10) {
            return 'yellow';
        } else {
            return 'green';
        }
    }

    /**
     * Scope for active products
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope for in-stock products
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope for low stock products
     */
    public function scopeLowStock($query)
    {
        return $query->where('stock', '>', 0)->where('stock', '<', 10);
    }

    /**
     * Scope for out of stock products
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('stock', '<=', 0);
    }

    /**
     * Update stock quantity
     */
    public function updateStock($quantity)
    {
        $this->update(['stock' => max(0, $this->stock + $quantity)]);
    }

    /**
     * Decrease stock (for orders)
     */
    public function decreaseStock($quantity)
    {
        $this->updateStock(-$quantity);
    }

    /**
     * Increase stock (for restocking)
     */
    public function increaseStock($quantity)
    {
        $this->updateStock($quantity);
    }
} 