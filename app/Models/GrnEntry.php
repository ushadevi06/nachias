<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrnEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'grn_number',
        'grn_date',
        'purchase_invoice_id',
        'supplier_id',
        'supplier_invoice_date',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'grn_date' => 'date',
        'supplier_invoice_date' => 'date',
    ];

    // Relationships
    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function grnEntryItems()
    {
        return $this->hasMany(GrnEntryItem::class);
    }
}
