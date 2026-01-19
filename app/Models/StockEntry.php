<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'stock_entry_no',
        'stock_date',
        'entry_type',
        'grn_entry_id',
        'from_store_location_id',
        'to_store_location_id',
        'remarks',
        'reference_document',
        'status',
        'created_by',
        'updated_by',
        'price',
    ];

    protected $casts = [
        'stock_date' => 'date',
    ];

    /**
     * Entry types that allow qty_in
     */
    public const QTY_IN_TYPES = [
        'Purchase Receipt',
        'Transfer Receipt',
        'Production Receipt',
        'Stock Adjustment',
        'Stock Conversion',
    ];

    /**
     * Entry types that allow qty_out
     */
    public const QTY_OUT_TYPES = [
        'Transfer Issue',
        'Production Issue',
        'Stock Adjustment',
        'Stock Conversion',
    ];

    // Relationships
    public function grnEntry()
    {
        return $this->belongsTo(GrnEntry::class);
    }

    public function fromStoreLocation()
    {
        return $this->belongsTo(StoreLocation::class, 'from_store_location_id');
    }

    public function toStoreLocation()
    {
        return $this->belongsTo(StoreLocation::class, 'to_store_location_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function stockEntryItems()
    {
        return $this->hasMany(StockEntryItem::class);
    }

    /**
     * Check if this entry type allows qty_in
     */
    public function allowsQtyIn(): bool
    {
        return in_array($this->entry_type, self::QTY_IN_TYPES);
    }

    /**
     * Check if this entry type allows qty_out
     */
    public function allowsQtyOut(): bool
    {
        return in_array($this->entry_type, self::QTY_OUT_TYPES);
    }
}
