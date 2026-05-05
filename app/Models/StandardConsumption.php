<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StandardConsumption extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'raw_material_id',
        'fs_qty',
        'hs_qty',
        'status',
        'created_by',
        'updated_by',
    ];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class, 'raw_material_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}
