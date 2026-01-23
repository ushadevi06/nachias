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
        'service_id',
        'adjustment_type',
        'qty',
        'approved_by',
        'reason',
        'created_by',
        'updated_by'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function service()
    {
        return $this->belongsTo(ProductionService::class, 'service_id');
    }
}
