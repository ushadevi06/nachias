<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'po_number',
        'po_date',
        'purchase_commission_agent_id',
        'commission',
        'supplier_id',
        'reference_no',
        'reference_date',
        'due_date',
        'store_type_id',

        'payment_terms',
        'status',
        'total_qty',
        'sub_total',
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
        'additional_attachments',
    ];

    protected $casts = [
        'po_date' => 'date',
        'reference_date' => 'date',
        'due_date' => 'date',

        'other_state' => 'boolean',
        'commission' => 'decimal:2',
        'total_qty' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'taxable_amount' => 'decimal:2',
        'igst_percent' => 'decimal:2',
        'cgst_percent' => 'decimal:2',
        'sgst_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'round_off' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function purchaseCommissionAgent()
    {
        return $this->belongsTo(SalesAgent::class, 'purchase_commission_agent_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function storeType()
    {
        return $this->belongsTo(StoreType::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}
