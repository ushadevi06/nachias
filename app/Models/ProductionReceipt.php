<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_card_id',
        'is_additional',
        'job_card_fabric_detail_id',
        'employee_id',
        'order_due_date',
        'receipt_no',
        'receipt_date',
        'doc_no',
        'doc_date',
        'store_type_id',
        'store_location_id',
        'warehouse_id',
        'status',
        'remarks',
        'created_by',
        'updated_by',
    ];

    public function additionalBatch()
    {
        return $this->belongsTo(JobCardFabricDetail::class, 'job_card_fabric_detail_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function jobCard()
    {
        return $this->belongsTo(JobCardEntry::class , 'job_card_id');
    }

    public function storeType()
    {
        return $this->belongsTo(StoreType::class , 'store_type_id');
    }

    public function storeLocation()
    {
        return $this->belongsTo(StoreLocation::class , 'store_location_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class , 'warehouse_id');
    }

    public function items()
    {
        return $this->hasMany(ProductionReceiptItem::class);
    }
}
