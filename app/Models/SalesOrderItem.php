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
        'category_name',
        'categories_path_val',
        'item_id',
        'color_id',
        'art_no',
        'item_name',
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
        'api_color',
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

    public function getArtNoAttribute($value)
    {
        if (!empty($value) && $value !== '-') {
            return $value;
        }

        $stockItem = null;
        if ($this->relationLoaded('stockEntryItem') && $this->stockEntryItem) {
            $stockItem = $this->stockEntryItem;
        } elseif ($this->stock_entry_item_id) {
            $stockItem = StockEntryItem::find($this->stock_entry_item_id);
        }

        if ($stockItem && !empty($stockItem->art_no)) {
            return $stockItem->art_no;
        }

        return $value ?? '-';
    }

    public function getItemNameAttribute($value)
    {
        if (!empty($value) && $value !== '-') {
            return $value;
        }

        $stockItem = null;
        if ($this->relationLoaded('stockEntryItem') && $this->stockEntryItem) {
            $stockItem = $this->stockEntryItem;
        } elseif ($this->stock_entry_item_id) {
            $stockItem = StockEntryItem::find($this->stock_entry_item_id);
        }

        if ($stockItem && !empty($stockItem->finished_item_code)) {
            return $stockItem->finished_item_code;
        }

        if ($stockItem && !empty($stockItem->art_no)) {
            return $stockItem->art_no;
        }

        return $this->sku ?? '-';
    }

    public function getFinishedItemCodeAttribute()
    {
        if ($this->stock_entry_item_id) {
            $stockItem = StockEntryItem::find($this->stock_entry_item_id);
            if ($stockItem) {
                return $stockItem->finished_item_code;
            }
        }
        if (!empty($this->sku)) {
            $stockItem = StockEntryItem::where(function ($q) {
                    $q->where('sku', $this->sku)
                      ->orWhere('barcode', $this->sku);
                })
                ->where('stock_type', 'finished_goods')
                ->first();
            if ($stockItem) {
                return $stockItem->finished_item_code;
            }
        }
        return $this->sku ?? '-';
    }
}
