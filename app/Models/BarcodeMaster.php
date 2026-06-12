<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarcodeMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_card_entry_id',
        'barcode_no',
        'item_code',
        'art_no',
        'item_name',
        'sleeve_type',
        'size',
        'quantity',
        'brand_id',
        'style_id',
        'lot_no',
        'color_id',
        'fabric_type_id'
    ];
}
