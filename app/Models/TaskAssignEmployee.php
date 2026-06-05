<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskAssignEmployee extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'task_assign_employees';

    protected $fillable = [
        'task_id',
        'issued_to',
        'service_id', 
        'issue_date',
        'due_date',
        'issue_qty',
        'total_hrs',
        'status',
        'remarks',
        'created_by',
        'updated_by',
        'completed_qty',
        'inprogress_qty',
        'wastage_qty',
        'qc_checked_qty',
        'qc_passed_qty',
        'qc_rejected_qty',
        'qc_status',
        'unit_rate',
        'total_cost'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'issued_to');
    }

    public function service()
    {
        return $this->belongsTo(ProductionService::class, 'service_id');
    }
    public function employee()
    {
        return $this->belongsTo(User::class, 'issued_to');
    }
}
