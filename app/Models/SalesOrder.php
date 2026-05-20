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
        'orderaxe_id',
        'so_date',
        'request_date',
        'order_type',
        'season_id',
        'customer_id',
        'customer_po_ref',
        'store_id',
        'agent_id',
        'zone_id',
        'delivery_date',
        'shipping_method_id',
        'transport_mode_id',
        'dispatch_from_id',
        'status',
        'total_qty',
        'sub_total_qty',
        'commission_percent',
        'commission_amount',
        'sales_discount_percent',
        'discount_percent',
        'discount_amount',
        'taxable_amount',
        'other_state',
        'igst_percent',
        'cgst_percent',
        'sgst_percent',
        'tax_amount',
        'other_charges',
        'round_off_type',
        'round_off',
        'total_amount',
        'billing_address',
        'shipping_address',
        'payment_terms',
        'transporter_name',
        'freight_type',
        'freight_amount',
        'transport_gst_no',

        'dispatch_through',
        'approved_by',
        'approved_date',
        'terms_conditions',
        'internal_remarks',
        'attachment',
        'created_by',
        'updated_by',
        'apply_box_discount',
        'order_no',
    ];

    protected $casts = [
        'so_date'         => 'date',
        'request_date'    => 'date',
        'delivery_date'   => 'date',
        'zone_id'         => 'integer',
        'shipping_method_id' => 'integer',
        'transport_mode_id'  => 'integer',
        'dispatch_from_id'   => 'integer',
        'other_state'     => 'boolean',
        'total_qty'       => 'decimal:2',
        'sub_total_qty'   => 'decimal:2',
        'commission_percent'=> 'decimal:2',
        'commission_amount' => 'decimal:2',
        'sales_discount_percent'=> 'decimal:2',
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
        'apply_box_discount' => 'boolean',
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

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id');
    }

    public function transportMode()
    {
        return $this->belongsTo(TransportMode::class, 'transport_mode_id');
    }

    public function dispatchFrom()
    {
        return $this->belongsTo(ServiceProvider::class, 'dispatch_from_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(SalesOrderItem::class, 'sale_order_id');
    }

    public function charges()
    {
        return $this->hasMany(SalesOrderCharge::class, 'sales_order_id');
    }

    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class, 'so_id');
    }

    public static function generateSoNo()
    {
        $setting = Setting::first();
        $prefix = ($setting && $setting->so_prefix) ? $setting->so_prefix : 'SO-';
        
        $lastSo = self::where('so_no', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        if ($lastSo) {
            $lastNumStr = substr($lastSo->so_no, strlen($prefix));
            $lastNum = is_numeric($lastNumStr) ? intval($lastNumStr) : 0;
            return $prefix . str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        }
        
        return $prefix . '0001';
    }
}
