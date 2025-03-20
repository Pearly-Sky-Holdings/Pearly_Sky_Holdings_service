<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalInformations extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'name',
        'age',
        'gender',
        'service_detail_id',
    ];

    public function serviceDetail(): BelongsTo
    {
        return $this->belongsTo(ServiceDetails::class);
    }
}
