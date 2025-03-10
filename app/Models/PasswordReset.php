<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $fillable = [
        'email',
        'otp',
        'user_type',
        'created_at',
        'expires_at'
    ];
    
    public $timestamps = false;
}