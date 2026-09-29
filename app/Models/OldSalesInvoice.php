<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OldSalesInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'old_sales_invoices';

    protected $fillable = [
        'doc_no',
        'doc_date',
        'customer_id',
        'customer_name',
        'gstin_reg_no',
        'vehicle_no',
        'pincode',
        'bill_to',
        'ship_to',
        'brand_id',
        'irn_no',
        'ack_no',
        'ack_date',
        'total_qty',
        'sub_total',
        'discount_amount',
        'taxable_amount',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'round_off',
        'total_amount',
        'created_by',
    ];

    protected $casts = [
        'doc_date' => 'date',
        'total_qty' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'taxable_amount' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'sgst_amount' => 'decimal:2',
        'igst_amount' => 'decimal:2',
        'round_off' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(OldSalesInvoiceItem::class, 'old_sales_invoice_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
