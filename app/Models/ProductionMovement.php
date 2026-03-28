<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionMovement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'production_movements';

    protected $fillable = [
        'job_card_id',
        'process_schedule_id',
        'operation_stage_id',
        'production_service_id',
        'task_id',
        'inward_qty',
        'outward_qty',
        'wastage_qty',
        'remarks',
        'created_by',
        'updated_by'
    ];

    public function jobCard()
    {
        return $this->belongsTo(JobCardEntry::class, 'job_card_id');
    }

    public function processSchedule()
    {
        return $this->belongsTo(ProcessSchedule::class, 'process_schedule_id');
    }

    public function operationStage()
    {
        return $this->belongsTo(OperationStage::class, 'operation_stage_id');
    }

    public function productionService()
    {
        return $this->belongsTo(ProductionService::class, 'production_service_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
