<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $primaryKey = 'client_id';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'billing_address',
        'shipping_address',
        'customer_type',
        'image',
    ];

    protected $casts = [
        'customer_type' => 'string',
    ];

    /**
     * Get the orders for the client.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'client_id', 'client_id');
    }

    /**
     * Get the contacts for the client.
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'client_id', 'client_id');
    }
} 