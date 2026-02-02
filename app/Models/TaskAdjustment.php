<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskAdjustment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'adjustment_no',
        'task_id',
        'job_card_id',
        'affected_stage',
        'service_id',
        'approved_by',
        'overall_reason',
        'status',
        'created_by',
        'updated_by'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function jobCard()
    {
        return $this->belongsTo(JobCardEntry::class, 'job_card_id');
    }

    public function service()
    {
        return $this->belongsTo(ProductionService::class, 'service_id');
    }

    public function items()
    {
        return $this->hasMany(TaskAdjustmentItem::class, 'task_adjustment_id');
    }
}
