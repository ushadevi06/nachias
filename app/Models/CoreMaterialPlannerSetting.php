<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoreMaterialPlannerSetting extends Model
{
    use SoftDeletes;

    protected $table = 'core_material_planner_settings';

    protected $fillable = [
        'art_no',
        'brand_id',
        'daily_consumption',
        'supplier_lead_time',
        'safety_stock',
        'status',
        'created_by',
        'updated_by',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
