<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Uom;

class ProductionReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_receipt_id',
        'item_id',
        'item_code',
        'item_name',
        'art_no',
        'size',
        'description',
        'size_variant',
        'unit_price',
        'total_value',
        'uom_id',
        'uom_code',
        'ordered_qty',
        'completed_qty',
        'qty_already_received',
        'scan_qty',
        'damage_qty',
        'qty_to_receive',
        'balance_qty',
    ];

    public function productionReceipt()
    {
        return $this->belongsTo(ProductionReceipt::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }
}
