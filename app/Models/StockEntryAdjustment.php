<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockEntryAdjustment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'adjustment_no',
        'stock_entry_item_id',
        'raw_material_id',
        'qty',
        'previous_stock',
        'new_stock',
        'approved_by',
        'reason',
        'status',
        'created_by',
        'updated_by'
    ];

    public function stockEntryItem()
    {
        return $this->belongsTo(StockEntryItem::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
