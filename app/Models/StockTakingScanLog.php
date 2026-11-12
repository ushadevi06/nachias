<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTakingScanLog extends Model
{
    protected $table = 'stock_taking_scan_logs';

    protected $fillable = [
        'session_id',
        'stock_taking_item_id',
        'store_location_id',
        'raw_code',
        'resolved_sku',
        'detected_brand_id',
        'detected_brand_name',
        'art_no',
        'size',
        'sleeve_type',
        'qty',
        'scan_status',
        'message',
        'scanned_by',
    ];

    protected $casts = [
        'qty' => 'float',
    ];

    public function session()
    {
        return $this->belongsTo(StockTakingSession::class, 'session_id');
    }

    public function item()
    {
        return $this->belongsTo(StockTakingItem::class, 'stock_taking_item_id');
    }

    public function scanner()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    public function storeLocation()
    {
        return $this->belongsTo(StoreLocation::class, 'store_location_id');
    }
}

