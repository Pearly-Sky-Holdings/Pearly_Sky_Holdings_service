<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'service_details_id',
        'qty',
        'price'
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class,"item_id");
    }

    /**
     * Get the service details for the item detail.
     */
    public function serviceDetail(): BelongsTo
    {
        return $this->belongsTo(ServiceDetails::class);
    }

}
