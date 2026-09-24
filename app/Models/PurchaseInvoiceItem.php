<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInvoiceItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purchase_invoice_id',
        'purchase_order_item_id',
        'raw_material_id',
        'hsn_code',
        'quantity',
        'uom_id',
        'rate',
        'amount',
        'cgst_percent',
        'cgst_amount',
        'sgst_percent',
        'sgst_amount',
        'igst_percent',
        'igst_amount',
        'qty_ordered',
        'qty_received',
        'qty_invoiced',
        'brand_id',
        'fabric_width_id',
        'notes',
    ];

    protected $casts = [
        'quantity'     => 'decimal:2',
        'rate'         => 'decimal:2',
        'amount'       => 'decimal:2',
        'cgst_percent' => 'decimal:2',
        'cgst_amount'  => 'decimal:2',
        'sgst_percent' => 'decimal:2',
        'sgst_amount'  => 'decimal:2',
        'igst_percent' => 'decimal:2',
        'igst_amount'  => 'decimal:2',
        'qty_ordered'  => 'decimal:2',
        'qty_received' => 'decimal:2',
        'qty_invoiced' => 'decimal:2',
    ];

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function fabricWidth()
    {
        return $this->belongsTo(FabricSize::class, 'fabric_width_id');
    }
}
