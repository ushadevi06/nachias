<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_id',
        'job_card_id',
        'customer_name',
        'order_due_date',
        'receipt_no',
        'receipt_date',
        'doc_no',
        'doc_date',
        'store_type_id',
        'status',
        'remarks',
        'created_by',
        'updated_by',
    ];

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function jobCard()
    {
        return $this->belongsTo(JobCardEntry::class, 'job_card_id');
    }

    public function storeType()
    {
        return $this->belongsTo(StoreType::class, 'store_type_id');
    }

    public function items()
    {
        return $this->hasMany(ProductionReceiptItem::class);
    }
}
