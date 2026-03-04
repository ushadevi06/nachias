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
        'customer_id',
        'reason',
        'other_state',
        'igst_percent',
        'igst',
        'cgst_percent',
        'cgst',
        'sgst_percent',
        'sgst',
        'sub_total',
        'tax_amount',
        'grand_total',
        'remarks',
        'reference_doc',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'note_date' => 'date',
        'other_state' => 'boolean',
    ];

    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(CreditNoteItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
