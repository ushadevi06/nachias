<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Charge extends Model
{
    use SoftDeletes;

    protected $fillable = ['charge_name', 'status','created_by','updated_by'];

    public function purchaseInvoiceCharges()
    {
        return $this->hasMany(PurchaseInvoiceCharge::class);
    }
}
