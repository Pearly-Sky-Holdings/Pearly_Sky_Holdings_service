<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReStockingChecklistDetails extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        're_stocking_checklist_id',
        'service_detail_id',
    ];

    public function serviceDetail(): BelongsTo
    {
        return $this->belongsTo(ServiceDetails::class);
    }

    public function reStocking()
    {
        return $this->belongsTo(ReStockingChecklist::class, 're_stocking_checklist_id');
    }
}
