<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemPrice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'finished_item_code',
        'art_no',
        'size',
        'unit_price',
        'selling_price',
        'effective_from',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'effective_from' => 'date',
    ];

}
