<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'inv_no',
        'inv_date',
        'so_id',
        'so_ids',
        'customer_id',
        'store_id',
        'agent_id',
        'delivery_address',
        'remarks',
        'invoice_status',
        'payment_mode',
        'extra_input',
        'due_date',
        'notes',
        'signature_file',
        'attachment_file',
        'show_fields',
        'sub_total',
        'discount_percent',
        'discount',
        'transporter_name',
        'tran_doc_no',
        'tran_doc_date',
        'veh_type',
        'lr_no',
        'no_of_box',
        'hsn_sac',
        'commission_percent',
        'commission_amount',
        'total',
        'other_state',
        'tax_amount',
        'igst_percent',
        'igst',
        'cgst_percent',
        'cgst',
        'sgst_percent',
        'sgst',
        'other_charges',
        'round_off_type',
        'round_off',
        'grand_total',
        'received_amount',
        'due_amount',
        'created_by',
        'updated_by',
        'irn',
        'ack_no',
        'ack_date',
        'signed_qr_code',
        'eway_bill_no',
        'eway_bill_date',
        'eway_bill_valid_till',
        'vehicle_no',
        'transporter_id',
        'transport_mode',
        'transport_distance',
    ];

    protected $casts = [
        'inv_date' => 'date',
        'due_date' => 'date',
        'other_state' => 'boolean',
        'show_fields' => 'array',
        'ack_date' => 'datetime',
        'eway_bill_date' => 'datetime',
        'eway_bill_valid_till' => 'datetime',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class, 'so_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SalesInvoiceItem::class, 'sales_invoice_id');
    }
}
