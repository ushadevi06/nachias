<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales_orders';

    protected $fillable = [
        'so_no',
        'so_date',
        'request_date',
        'order_type',
        'season_id',
        'customer_id',
        'customer_po_ref',
        'store_id',
        'agent_id',
        'delivery_date',
        'shipping_method',
        'transport_mode',
        'dispatch_from',
        'status',
        'total_qty',
        'sub_total_qty',
        'commission_percent',
        'commission_amount',
        'discount_percent',
        'discount_amount',
        'taxable_amount',
        'other_state',
        'igst_percent',
        'cgst_percent',
        'sgst_percent',
        'tax_amount',
        'round_off_type',
        'round_off',
        'total_amount',
        'billing_address',
        'shipping_address',
        'payment_terms',
        'transporter_name',
        'freight_type',
        'freight_amount',
        'eway_bill_no',
        'lr_no',
        'dispatch_through',
        'approved_by',
        'approved_date',
        'terms_conditions',
        'internal_remarks',
        'attachment',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'so_date'         => 'date',
        'request_date'    => 'date',
        'delivery_date'   => 'date',
        'other_state'     => 'boolean',
        'total_qty'       => 'decimal:2',
        'sub_total_qty'   => 'decimal:2',
        'commission_percent'=> 'decimal:2',
        'commission_amount' => 'decimal:2',
        'discount_percent'=> 'decimal:2',
        'discount_amount' => 'decimal:2',
        'taxable_amount'  => 'decimal:2',
        'igst_percent'    => 'decimal:2',
        'cgst_percent'    => 'decimal:2',
        'sgst_percent'    => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'round_off'       => 'decimal:2',
        'freight_amount'  => 'decimal:2',
        'approved_date'   => 'datetime',
        'total_amount'    => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function salesAgent()
    {
        return $this->belongsTo(SalesAgent::class, 'agent_id');
    }

    public function store()
    {
        return $this->belongsTo(StoreType::class, 'store_id');
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(SalesOrderItem::class, 'sale_order_id');
    }
}
