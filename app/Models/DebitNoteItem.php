<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DebitNoteItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'debit_note_id',
        'purchase_invoice_item_id',
        'raw_material_id',
        'quantity',
        'uom_id',
        'rate',
        'amount',
    ];

    public function debitNote()
    {
        return $this->belongsTo(DebitNote::class);
    }

    public function purchaseInvoiceItem()
    {
        return $this->belongsTo(PurchaseInvoiceItem::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class);
    }
}
