<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FgMinStock extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'stock_entry_item_id',
        'min_stock',
        'status',
        'created_by',
        'updated_by',
    ];

    public function stockEntryItem()
    {
        return $this->belongsTo(StockEntryItem::class, 'stock_entry_item_id');
    }
}
