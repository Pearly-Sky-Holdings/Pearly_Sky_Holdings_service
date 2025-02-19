<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Service extends Model
{
    use HasFactory, HasApiTokens;

    protected $primaryKey = 'service_id';
    public $incrementing = true;

    protected $fillable = [
        'name',
        'price',
        'status'
    ];

    public function serviceDetails()
    {
        return $this->hasMany(ServiceDetails::class);
    }
}
