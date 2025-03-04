<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'status', // pending, completed, failed, cancelled
        'transaction_id', // PayPal order ID or Stripe session ID
        'payment_data', // JSON data with payment details
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}