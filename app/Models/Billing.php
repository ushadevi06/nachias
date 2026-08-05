<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Billing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bill_no',
		'billing_name',
        'billing_type',
        'bill_date',
        'amount',
        'reason',
        'status',
    ];

    protected $casts = [
        'bill_date' => 'date',
        'amount'    => 'decimal:2',
    ];
}
