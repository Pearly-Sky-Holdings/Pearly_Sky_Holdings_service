<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory, HasApiTokens;

    protected $primaryKey = 'employee_id';
    public $incrementing = true;

    protected $fillable = [
        'name',
        'age',
        'address',
        'position',
        'contact',
        'email',
        'status',
        'password'
    ];

    protected $hidden = [
        'password'
    ];

    // Password will be automatically hashed when set
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}
