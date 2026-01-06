<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RawMaterial extends Model
{
    use SoftDeletes;

    protected $table = 'raw_materials';

    protected $fillable = [
        'store_category_id',
        'code',
        'name',
        'supplier_design_name',
        'supplier_design_name',
        'size_width',
        'uom_id',
        'fabric_type_id',
        'reference_image',
        'specification',
        'min_stock',
        'status',
        'created_by',
    ];

    protected $dates = ['deleted_at'];

    public function storeCategory()
    {
        return $this->belongsTo(StoreCategory::class, 'store_category_id');
    }


    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }

    public function fabricType()
    {
        return $this->belongsTo(FabricType::class, 'fabric_type_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
