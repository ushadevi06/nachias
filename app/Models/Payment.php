<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_no',
        'payment_type',
        'reference_type',
        'reference_id',
        'reference_no',
        'payment_mode',
        'amount',
        'payment_date',
        'transaction_no',
        'bank_name',
        'cheque_no',
        'cheque_date',
        'attachment',
        'remarks',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'cheque_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'reference_id');
    }

    public function debitNote()
    {
        return $this->belongsTo(DebitNote::class, 'reference_id');
    }
}
