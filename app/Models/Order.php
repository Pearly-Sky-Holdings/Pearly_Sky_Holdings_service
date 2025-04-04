<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';
    public $incrementing = true;

    protected $fillable = [
        'customer_id',
        'date',
        'time',
        'price',
        'qr_code',
        'status'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function serviceDetails()
    {
        return $this->hasMany(ServiceDetails::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
