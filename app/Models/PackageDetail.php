<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PackageDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'package_id',
        'service_detail_id',
        'price',
        'qty'
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Packege::class, 'package_id');
    }

    public function serviceDetail(): BelongsTo
    {
        return $this->belongsTo(ServiceDetails::class);
    }
}
