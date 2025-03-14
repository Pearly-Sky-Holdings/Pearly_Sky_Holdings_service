<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReStockingChecklist extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'name',
        'category',
        'type'
    ];

    public function serviceWithReStocking(): HasMany
    {
        return $this->hasMany(ReStockingChecklistDetails::class);
    }
}
