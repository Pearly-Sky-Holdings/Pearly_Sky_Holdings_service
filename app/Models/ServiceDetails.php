<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceDetails extends Model
{

    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'order_id',
        'customer_id',
        'service_id',
        'price',
        'date',
        'time',
        'property_size',
        'duration',
        'number_of_cleaners',
        'note',
        'person_type',
        'request_language',
        'business_property',
        'frequency',
        'request_gender',
        'cleaning_solvents',
        'Equipment',
        'status'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function packageDetails(): HasMany
    {
        return $this->hasMany(PackageDetail::class,"id");
    }

    public function ItemDetails(): HasMany
    {
        return $this->hasMany(ItemDetails::class,"id");
    }

    public function serviceWithReStocking()
    {
        return $this->hasMany(ReStockingChecklistDetails::class);
    }

    public function serviceDetails()
    {
        return $this->hasMany(ServiceDetails::class);
    }
}
