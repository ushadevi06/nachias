<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionService extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_name',
        'service_code',
        'operation_stage_id',
        'status',
        'applies_to',
        'base_quantity_source',
        'multiplier',
        'uom',
        'created_by',
        'updated_by',
    ];

    public function operationStage()
    {
        return $this->belongsTo(OperationStage::class, 'operation_stage_id');
    }
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}

