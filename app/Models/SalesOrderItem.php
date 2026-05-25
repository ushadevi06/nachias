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
        'commission_percent',
        'commission_amount',
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

    public function getItemNameAttribute()
    {
        // 1. Try to fetch from BarcodeMaster using SKU (barcode_no)
        if (!empty($this->sku)) {
            $barcode = BarcodeMaster::where('barcode_no', $this->sku)->first();
            if ($barcode && !empty($barcode->item_name)) {
                return $barcode->item_name;
            }
        }

        // 2. Try to fetch from StockEntryItem -> finished_item_code
        if ($this->stockEntryItem && !empty($this->stockEntryItem->finished_item_code)) {
            return $this->stockEntryItem->finished_item_code;
        }

        // 3. Fallback to art_no, sku, or '-' (do NOT use items table)
        return $this->art_no ?? $this->sku ?? '-';
    }
}
