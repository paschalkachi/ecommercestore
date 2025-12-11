<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withDefault();
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    // allow mass assignment for fields created from order items
    protected $fillable = [
        'product_id',
        'order_id',
        'price',
        'quantity',
        'options',
        'rstatus',
    ];

    // cast types for convenient access
    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'options' => 'array',
        'rstatus' => 'boolean',
    ];
}
