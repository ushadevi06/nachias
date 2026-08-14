<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesInvoiceItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sales_invoice_id',
        'sku',
        'uom_id',
        'quantity',
        'rate',
        'mrp',
        'amount',
        'color_id',
        'api_color',
        'hsn_sac',
        'art_no',
        'size',
        'sleeve_type',
        'stock_entry_item_id',
        'scanned_qty',
        'is_extra',
    ];

    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function brandCategory()
    {
        return $this->belongsTo(BrandCategory::class, 'brand_id');
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class);
    }

    public function sizeRatio()
    {
        return $this->belongsTo(SizeRatio::class, 'size');
    }

    public function stockEntryItem()
    {
        return $this->belongsTo(StockEntryItem::class);
    }

    public function getArtNoAttribute($value)
    {
        $stockItem = null;
        if ($this->relationLoaded('stockEntryItem') && $this->stockEntryItem) {
            $stockItem = $this->stockEntryItem;
        } elseif ($this->stock_entry_item_id) {
            $stockItem = StockEntryItem::find($this->stock_entry_item_id);
        }
        if (!$stockItem && !empty($this->sku)) {
            $stockItem = StockEntryItem::where(function ($q) {
                    $q->where('sku', $this->sku)
                      ->orWhere('barcode', $this->sku);
                })
                ->where('stock_type', 'finished_goods')
                ->first();
        }

        if ($stockItem && !empty($stockItem->art_no)) {
            return $stockItem->art_no;
        }

        return $value;
    }
}
