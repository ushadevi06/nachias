<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purchase_order_id',
        'store_category_id',
        'raw_material_id',
        'uom_id',
        'color_id',
        'style_id',
        'quantity',
        'art_no',
        'rate',
        'amount',
        'remarks',
        'attached_file',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function storeCategory()
    {
        return $this->belongsTo(StoreCategory::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function style()
    {
        return $this->belongsTo(Style::class);
    }
}
