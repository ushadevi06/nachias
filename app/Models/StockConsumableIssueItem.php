<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockConsumableIssueItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'stock_consumable_issue_id',
        'raw_material_id',
        'stock_entry_item_id',
        'qty_issued',
        'qty_returned',
        'net_consumption',
        'return_reason',
        'uom_id',
        'unit_price',
        'total_value',
        'created_by',
        'updated_by',
    ];

    public function issue()
    {
        return $this->belongsTo(StockConsumableIssue::class, 'stock_consumable_issue_id');
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function stockEntryItem()
    {
        return $this->belongsTo(StockEntryItem::class);
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class);
    }

    public function stockDetails()
    {
        return $this->hasMany(StockConsumableStockDetail::class);
    }
}
