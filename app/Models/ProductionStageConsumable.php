<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionStageConsumable extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_card_id',
        'production_id',
        'production_stage_id',
        'stage',
        'art_no',
        'item_type',
        'raw_material_id',
        'quantity_per_unit',
        'planned_qty',
        'fs_qty',
        'hs_qty',
        'total_qty',
        'actual_qty',
        'uom_id',
        'sleeve_type',
        'status',
        'remarks',
        'created_by',
        'updated_by',
    ];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function production()
    {
        return $this->belongsTo(Production::class, 'production_id');
    }

    public function jobCard()
    {
        return $this->belongsTo(JobCardEntry::class, 'job_card_id');
    }

    public function operationStage()
    {
        return $this->belongsTo(OperationStage::class, 'production_stage_id');
    }
}
