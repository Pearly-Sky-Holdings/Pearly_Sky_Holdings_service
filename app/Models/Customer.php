<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Model
{
    use HasFactory, HasApiTokens;

    protected $primaryKey = 'customer_id';
    public $incrementing = true;

    protected $fillable = [
        'first_name',
        'last_name',
        'company',
        'country',
        'city',
        'province',
        'postal_code',
        'contact',
        'email',
        'password'
    ];

    protected $hidden = [
        'password'
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id', 'customer_id');
    }

    public function serviceDetails()
    {
        return $this->hasMany(ServiceDetails::class);
    }
}
