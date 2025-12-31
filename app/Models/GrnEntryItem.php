<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrnEntryItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'grn_entry_id',
        'purchase_invoice_item_id',
        'art_no',
        'fabric_type_id',
        'qty_ordered',
        'qty_received',
        'qty_accepted',
        'qty_rejected',
        'qty_balanced',
        'rate',
        'amount',
        'quality_check_status',
        'store_location_id',
        'image',
    ];

    // Relationships
    public function grnEntry()
    {
        return $this->belongsTo(GrnEntry::class);
    }

    public function purchaseInvoiceItem()
    {
        return $this->belongsTo(PurchaseInvoiceItem::class);
    }

    public function fabricType()
    {
        return $this->belongsTo(FabricType::class);
    }

    public function storeLocation()
    {
        return $this->belongsTo(StoreLocation::class);
    }

    public function variants()
    {
        return $this->hasMany(GrnEntryItemVariant::class);
    }

    public function stockEntryItems()
    {
        return $this->hasMany(StockEntryItem::class);
    }
}
