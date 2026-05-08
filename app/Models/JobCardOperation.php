<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCardOperation extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_card_entry_id', 'operation_stage_id', 'service_provider_id', 'employee_id', 'assigned_date', 'deadline_date', 'received_by', 'remarks', 'rate', 'total_cost'
    ];

    public function jobCardEntry()
    {
        return $this->belongsTo(JobCardEntry::class);
    }

    public function operationStage()
    {
        return $this->belongsTo(OperationStage::class);
    }

    public function stage()
    {
        return $this->belongsTo(OperationStage::class, 'operation_stage_id');
    }

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class, 'service_provider_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
