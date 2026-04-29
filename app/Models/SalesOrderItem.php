<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales_order_items';

    protected $fillable = [
        'sale_order_id',
        'brand_cat_id',
        'item_id',
        'color_id',
        'art_no',
        'uom_id',
        'size_id',
        'qty',
        'rate',
        'mrp',
        'amount',
        'sleeve',
        'stock_entry_item_id',
        'sku',
    ];

    protected $casts = [
        'qty'    => 'decimal:2',
        'rate'   => 'decimal:2',
        'mrp'    => 'decimal:2',
        'amount' => 'decimal:2',
        'sleeve' => 'array',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class, 'sale_order_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function brandCategory()
    {
        return $this->belongsTo(BrandCategory::class, 'brand_cat_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class);
    }
    public function size()
    {
        return $this->belongsTo(SizeRatio::class, 'size_id');
    }

    public function stockEntryItem()
    {
        return $this->belongsTo(StockEntryItem::class);
    }
}
