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
        'production_id',
        'production_no',
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
        'created_by',
        'updated_by'
    ];
    
    protected $casts = [
        'services' => 'array'
    ];

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

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

    public function receives()
    {
        return $this->hasMany(TaskReceive::class, 'task_id');
    }

    public function adjustments()
    {
        return $this->hasMany(TaskAdjustment::class, 'task_id');
    }
}
