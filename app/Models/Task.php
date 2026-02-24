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
        'services',
        'issued_to',
        'issue_date',
        'due_date',
        'issue_qty',
        'issue_store',
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

    public function stage()
    {
        return $this->belongsTo(ProcessSchedule::class, 'stage_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'issued_to');
    }
    
    public function adjustments()
    {
        return $this->hasMany(TaskAdjustment::class, 'task_id');
    }

    public function assignments()
    {
        return $this->hasMany(TaskAssignEmployee::class, 'task_id');
    }
    
    public function operationStage()
    {
        return $this->belongsTo(OperationStage::class, 'stage_id');
    }
}
