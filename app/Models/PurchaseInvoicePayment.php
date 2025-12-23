<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInvoicePayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purchase_invoice_id',
        'amount',
        'payment_date',
        'payment_mode',
        'transaction_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }
}
