<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInvoiceCharge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purchase_invoice_id',
        'charge_id',
        'charge_name',
        'charge_amount',
    ];

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }
    protected $dates = ['deleted_at'];
}
