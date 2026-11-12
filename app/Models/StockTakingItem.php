<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTakingItem extends Model
{
    protected $table = 'stock_taking_items';

    protected $fillable = [
        'session_id',
        'sku',
        'style_id',
        'style_name',
        'art_no',
        'finished_item_code',
        'size',
        'sleeve_type',
        'system_store_location_id',
        'store_location_id',
        'system_qty',
        'scanned_qty',
        'rate',
        'last_scanned_at',
    ];

    protected $casts = [
        'system_qty' => 'float',
        'scanned_qty' => 'float',
        'rate' => 'float',
        'last_scanned_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(StockTakingSession::class, 'session_id');
    }

    public function systemStoreLocation()
    {
        return $this->belongsTo(StoreLocation::class, 'system_store_location_id');
    }

    public function storeLocation()
    {
        return $this->belongsTo(StoreLocation::class, 'store_location_id');
    }
}

