<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    // allow mass assignment for the fields created by your controller/form
    protected $fillable = [
        'user_id',
        'sub_total',
        'discount',
        'tax',
        'total',
        'name',
        'phone',
        'locality',
        'address',
        'city',
        'state',
        'country',
        'landmark',
        'zip',
        'type',
        'status',
        'is_shippin_different',
        'delivered_date',
        'canceled_date',
    ];

    protected $casts = [
        'sub_total' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'is_shippin_different' => 'boolean',
        'delivered_date' => 'date',
        'canceled_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Backwards-compatible singular accessor used in the view: $order->orderItem
    public function orderItem()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function Transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}

