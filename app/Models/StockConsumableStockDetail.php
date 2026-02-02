<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockConsumableStockDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_consumable_issue_item_id',
        'stock_entry_item_id',
        'qty',
    ];

    public function issueItem()
    {
        return $this->belongsTo(StockConsumableIssueItem::class, 'stock_consumable_issue_item_id');
    }

    public function stockEntryItem()
    {
        return $this->belongsTo(StockEntryItem::class);
    }
}
