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
        'brand_id',
        'item_id',
        'uom_id',
        'quantity',
        'rate',
        'mrp',
        'amount',
        'color_id',
        'hsn_sac',
        'art_no',
        'size',
        'sleeve_type',
        'stock_entry_item_id',
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
}
