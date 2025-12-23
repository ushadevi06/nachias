<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'brand_category_id',
        'brand_id',
        'name',
        'code',
        'entry_type',
        'style',
        'fabric_type_id',
        'design_art_no',
        'uom_id',
        'size_ratio_id',
        'color_id',
        'product_barcode',
        'standard_costing',
        'store_category_id',
        'related_materials',
        'operation_stages',
        'service_providers',
        'wholesale_price',
        'retail_price',
        'export_price',
        'status',
        'created_by',
    ];

    protected $casts = [
        'color_id' => 'array',
        'related_materials' => 'array',
        'operation_stages'  => 'array',
        'service_providers' => 'array',
    ];

    // public function getColorsAttribute()
    // {
    //     if (!$this->color_id) {
    //         return [];
    //     }
    //     return explode(',', $this->color_id);
    // }

    public function setColorIdAttribute($value)
    {
        $this->attributes['color_id'] = is_array($value)
            ? implode(',', $value)
            : $value;
    }

    public function getColorIdAttribute($value)
    {
        return $value ? explode(',', $value) : [];
    }

    public function brandCategory()
    {
        return $this->belongsTo(BrandCategory::class, 'brand_category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function fabricType()
    {
        return $this->belongsTo(FabricType::class, 'fabric_type_id');
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }

    public function sizeRatio()
    {
        return $this->belongsTo(SizeRatio::class, 'size_ratio_id');
    }
    public function storeCategory()
    {
        return $this->belongsTo(StoreCategory::class, 'store_category_id');
    }

    public static function generateCode()
    {
        $prefix = '8901';
        $lastItem = self::withTrashed()->orderBy('product_barcode', 'desc')->first();

        if ($lastItem && $lastItem->product_barcode) {
            $lastNumber = (int) substr($lastItem->product_barcode, 4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $barcode = $prefix . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);

        while (self::withTrashed()->where('product_barcode', $barcode)->exists()) {
            $nextNumber++;
            $barcode = $prefix . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);
        }

        return $barcode;
    }
}