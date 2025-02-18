<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Packege extends Model
{
    use HasFactory, HasApiTokens;

    protected $primaryKey = 'package_id';
    protected $table = 'packages';
    public $incrementing = true;

    protected $fillable = [
        'name',
        'price',
        'status'
    ];
}
