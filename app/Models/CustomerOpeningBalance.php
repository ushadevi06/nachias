<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerOpeningBalance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customer_opening_balances';

    protected $fillable = [
        'customer_id',
        'customer_code',
        'customer_name',
        'open_qty',
        'open_amount',
        'as_on_date',
        'financial_year',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'open_qty'    => 'decimal:2',
        'open_amount' => 'decimal:2',
        'as_on_date'  => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
