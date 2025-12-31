<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockEntryItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'stock_entry_id',
        'stock_type',
        'grn_entry_item_id',
        'raw_material_id',
        'finished_item_code', // TEMPORARY: Replace with finished_good_id FK later
        'store_category_id',
        'store_location_id',
        'uom_id',
        'qty_in',
        'qty_out',
        'price',
    ];

    // Relationships
    public function stockEntry()
    {
        return $this->belongsTo(StockEntry::class);
    }

    public function grnEntryItem()
    {
        return $this->belongsTo(GrnEntryItem::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function storeCategory()
    {
        return $this->belongsTo(StoreCategory::class);
    }

    public function storeLocation()
    {
        return $this->belongsTo(StoreLocation::class);
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class);
    }

    /**
     * Get the net quantity (in - out)
     */
    public function getNetQtyAttribute(): float
    {
        return $this->qty_in - $this->qty_out;
    }
}
