<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'warehouse_name',
        'code',
        'address',
        'status',
        'created_by',
        'updated_by',
    ];

    public function brandCapacities()
    {
        return $this->hasMany(WarehouseBrandCapacity::class, 'warehouse_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function getTotalCapacityAttribute()
    {
        return (int) $this->brandCapacities()->where('status', 'Active')->sum('capacity_pcs');
    }
}
