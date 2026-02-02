<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskAdjustmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_adjustment_id',
        'raw_material_id',
        'material_category',
        'adjustment_type',
        'qty',
        'uom_id',
        'store_id',
        'remarks',
        'previous_stock',
        'new_stock'
    ];

    public function adjustment()
    {
        return $this->belongsTo(TaskAdjustment::class, 'task_adjustment_id');
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class, 'raw_material_id');
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }

    public function store()
    {
        return $this->belongsTo(StoreType::class, 'store_id');
    }
}
