<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // allow mass assignment only for columns present in the migration
    protected $fillable = [
        'user_id',
        'order_id',
        'gateway',           // payment gateway (paystack, flutterwave, etc)
        'method',            // payment method (cod, paystack, bank_transfer)
        'reference',         // transaction reference from gateway
        'status',            // pending, paid, failed, refunded
        'gateway_response',  // JSON response from gateway API
    ];

    protected $casts = [
        'user_id' => 'integer',
        'order_id' => 'integer',
        'gateway_response' => 'array',   // IMPORTANT
    ];
    public function order(){
        return $this->belongsTo(Order::class);
    }

}
