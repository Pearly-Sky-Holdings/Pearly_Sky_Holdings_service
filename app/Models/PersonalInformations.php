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
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'contact',
        'address',
        'apartment_type',
        'city',
        'province',
        'postal_code',
        'contry',
        'request_care_professional',
        'gender',
        'age',
        'special_request',
        'service_providing_place',
        'service_detail_id',
    ];

    public function serviceDetail(): BelongsTo
    {
        return $this->belongsTo(ServiceDetails::class);
    }
}
