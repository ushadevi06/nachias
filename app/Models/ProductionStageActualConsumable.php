<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionStageActualConsumable extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_card_id',
        'production_id',
        'production_stage_id',
        'material_id',
        'planned_qty',
        'actual_qty',
        'uom',
        'status',
    ];

    public function jobCard()
    {
        return $this->belongsTo(JobCardEntry::class, 'job_card_id');
    }

    public function production()
    {
        return $this->belongsTo(Production::class, 'production_id');
    }

    public function operationStage()
    {
        return $this->belongsTo(OperationStage::class, 'production_stage_id');
    }

    public function material()
    {
        return $this->belongsTo(RawMaterial::class, 'material_id');
    }
}
