<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'driver_name',
        'loading_unloading_amount',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'loading_unloading_amount' => 'decimal:2',
        'status' => 'string',
    ];
}

