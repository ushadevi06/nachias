<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'note_no',
        'note_date',
        'sales_invoice_id',
        'sales_invoice_ids',
        'customer_id',
        'reason',
        'fault',
        'reason_detail',
        'zone_id',
        'agent_id',
        'other_state',
        'igst_percent',
        'igst',
        'cgst_percent',
        'cgst',
        'sgst_percent',
        'sgst',
        'sub_total',
        'discount_percent',
        'discount',
        'tax_amount',
        'other_charges',
        'round_off',
        'round_off_type',
        'grand_total',
        'remarks',
        'reference_doc',
        'status',
        'is_stock_updated',
        'show_fields',
        'created_by',
        'updated_by',
        'irn',
        'ack_no',
        'ack_date',
        'signed_qr_code',
        'einvoice_status',
        'eway_bill_no',
        'eway_bill_date',
        'eway_bill_valid_till',
    ];

    protected $casts = [
        'note_date' => 'date',
        'other_state' => 'boolean',
        'sales_invoice_ids' => 'array',
        'show_fields' => 'array',
    ];

    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function salesAgent()
    {
        return $this->belongsTo(SalesAgent::class, 'agent_id');
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function items()
    {
        return $this->hasMany(CreditNoteItem::class);
    }

    public function charges()
    {
        return $this->hasMany(CreditNoteCharge::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
