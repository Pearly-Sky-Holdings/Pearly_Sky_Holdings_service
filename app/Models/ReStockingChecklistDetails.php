<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReStockingChecklistDetails extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        're_stocking_checklist_id',
        'service_id',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function reStocking()
    {
        return $this->belongsTo(ReStockingChecklist::class, 're_stocking_checklist_id');
    }
}
