<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class JobApplication extends Model
{
    use HasFactory, HasApiTokens;

    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'first_name',
        'last_name',
        'company',
        'country',
        'city',
        'province',
        'street_address',
        'apartment_type',
        'postal_code',
        'contact',
        'email',
        "pdf"
    ];

}
