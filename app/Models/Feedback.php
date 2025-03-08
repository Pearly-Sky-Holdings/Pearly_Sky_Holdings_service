<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Feedback extends Model
{
    use HasFactory;
    protected $primaryKey = 'employee_id';
    public $incrementing = true;

    protected $fillable = [
        'star_count',
        'name',
        'description',
        'date',
        'social_media_type',
    ];
}
