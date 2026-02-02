<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionStageConsumable extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'stage',
        'raw_material_id',
        'quantity_per_unit',
        'uom_id',
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
}
