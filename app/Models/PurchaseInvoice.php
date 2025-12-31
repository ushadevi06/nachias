<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_no',
        'invoice_date',
        'purchase_order_id',
        'supplier_id',
        'consignee_location',
        'dispatch_location',
        'po_reference',
        'sub_total',
        'discount_percent',
        'discount_amount',
        'taxable_amount',
        'other_state',
        'igst_percent',
        'igst_amount',
        'cgst_percent',
        'cgst_amount',
        'sgst_percent',
        'sgst_amount',
        'tax_amount',
        'other_charges',
        'round_off',
        'round_off_type',
        'grand_total',
        'received_amount',
        'due_amount',
        'invoice_status',
        'payment_mode',
        'transaction_id',
        'due_date',
        'notes',
        'auth_signature',
        'attachments',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'other_state' => 'boolean',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }

    public function charges()
    {
        return $this->hasMany(PurchaseInvoiceCharge::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchaseInvoicePayment::class);
    }
}
