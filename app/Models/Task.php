<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'task_no',
        'job_card_entry_id',
        'job_card_no',
        'stage_id',
        'is_additional',
        'job_card_fabric_detail_id',
        'services',
        'issued_to',
        'issue_date',
        'due_date',
        'issue_qty',
        'issued_by',
        'remarks',
        'status',
        'total_hrs',
        'created_by',
        'updated_by'
    ];
    
    protected $casts = [
        'services' => 'array'
    ];

    public function jobCard()
    {
        return $this->belongsTo(JobCardEntry::class, 'job_card_entry_id');
    }

    public function additionalBatch()
    {
        return $this->belongsTo(JobCardFabricDetail::class, 'job_card_fabric_detail_id');
    }

    public function stage()
    {
        return $this->belongsTo(ProcessSchedule::class, 'stage_id')->withTrashed();
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'issued_to');
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
    
    public function adjustments()
    {
        return $this->hasMany(TaskAdjustment::class, 'task_id');
    }

    public function assignments()
    {
        return $this->hasMany(TaskAssignEmployee::class, 'task_id');
    }

    /**
     * The operation stage resolved through the associated ProcessSchedule (stage).
     * tasks.stage_id → process_schedules.id → process_schedules.operation_stage_id → operation_stages.id
     *
     * NOTE: hasOneThrough does not support withTrashed on the intermediate model,
     * so stage resolution is handled directly in the controller/repository layer.
     */
    public function operationStage()
    {
        return $this->hasOneThrough(
            OperationStage::class,  
            ProcessSchedule::class,  
            'id',                   
            'id',                   
            'stage_id',             
            'operation_stage_id'     
        );
    }

    public function getEffectiveStageAttribute()
    {
        if ($this->stage) {
            return $this->stage;
        }
        return null;
    }
}
