<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcessSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_card_entry_id', 'operation_stage_id', 'stage', 'planned_qty', 'uom', 'scheduled_to',
        'service_provider_type', 'start_date', 'end_date', 'due_date', 'status',
        'created_by', 'updated_by'
    ];

    public function jobCard()
    {
        return $this->belongsTo(JobCardEntry::class, 'job_card_entry_id');
    }

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class, 'scheduled_to');
    }

    public function operationStage()
    {
        return $this->belongsTo(OperationStage::class, 'operation_stage_id');
    }
}
