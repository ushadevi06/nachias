<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockTakingSession extends Model
{
    use SoftDeletes;

    protected $table = 'stock_taking_sessions';

    protected $fillable = [
        'session_no',
        'brand_id',
        'warehouse_id',
        'store_type_id',
        'store_location_id',
        'title',
        'remarks',
        'is_blind',
        'status',
        'total_system_skus',
        'total_system_qty',
        'total_scanned_qty',
        'started_by',
        'started_at',
        'completed_by',
        'completed_at',
    ];

    protected $casts = [
        'is_blind' => 'boolean',
        'total_system_skus' => 'integer',
        'total_system_qty' => 'float',
        'total_scanned_qty' => 'float',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function storeType()
    {
        return $this->belongsTo(StoreType::class, 'store_type_id');
    }

    public function storeLocation()
    {
        return $this->belongsTo(StoreLocation::class, 'store_location_id');
    }

    public function starter()
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function items()
    {
        return $this->hasMany(StockTakingItem::class, 'session_id');
    }

    public function scanLogs()
    {
        return $this->hasMany(StockTakingScanLog::class, 'session_id');
    }
}

