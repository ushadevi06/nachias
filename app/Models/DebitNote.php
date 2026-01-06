<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DebitNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'debit_note_no',
        'debit_note_date',
        'purchase_invoice_id',
        'supplier_id',
        'reason',
        'other_state',
        'igst_percent',
        'cgst_percent',
        'sgst_percent',
        'sub_total',
        'tax_amount',
        'other_charges',
        'round_off_type',
        'round_off',
        'grand_total',
        'remarks',
        'reference_document',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'debit_note_date' => 'date',
    ];

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(DebitNoteItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
