<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditNoteItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'credit_note_id',
        'sales_invoice_item_id',
        'item_id',
        'brand_category_id',
        'size',
        'quantity',
        'sleeve_type',
        'mrp',
        'uom_id',
        'rate',
        'amount',
    ];

    public function creditNote()
    {
        return $this->belongsTo(CreditNote::class);
    }

    public function salesInvoiceItem()
    {
        return $this->belongsTo(SalesInvoiceItem::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class);
    }

    public function brandCategory()
    {
        return $this->belongsTo(BrandCategory::class, 'brand_category_id');
    }
}
