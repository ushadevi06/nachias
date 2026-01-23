<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcessSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'production_id', 'stage', 'planned_qty', 'uom', 'scheduled_to',
        'service_provider_type', 'start_date', 'end_date', 'due_date', 'status',
        'created_by', 'updated_by'
    ];

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function services()
    {
        return $this->hasMany(ProcessScheduleService::class);
    }

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class, 'scheduled_to', 'name');
    }

    public function operationStage()
    {
        return $this->belongsTo(OperationStage::class, 'stage');
    }
}
