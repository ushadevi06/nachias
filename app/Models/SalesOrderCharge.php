<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrderCharge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sales_order_id',
        'charge_id',
        'charge_name',
        'charge_amount',
        'tax_type',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }
}
