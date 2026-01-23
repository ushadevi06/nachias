<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Production extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_card_entry_id', 'job_card_no', 'purchase_order_id', 'purchase_order_no',
        'plant_id', 'process_group_id', 'full_sleeve_qty', 'half_sleeve_qty',
        'total_planned_qty', 'planned_start_date', 'planned_end_date',
        'expected_completion_date', 'status', 'remarks', 'created_by', 'updated_by'
    ];

    public function jobCard()
    {
        return $this->belongsTo(JobCardEntry::class, 'job_card_entry_id');
    }

    public function plant()
    {
        return $this->belongsTo(ServiceProvider::class, 'plant_id');
    }

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class, 'plant_id');
    }

    public function processGroup()
    {
        return $this->belongsTo(ProcessGroup::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function processSchedules()
    {
        return $this->hasMany(ProcessSchedule::class);
    }
}
