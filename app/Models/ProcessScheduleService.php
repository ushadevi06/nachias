<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessScheduleService extends Model
{
    use HasFactory;

    protected $fillable = [
        'process_schedule_id', 'service_id', 'applies_to', 'calculated_qty', 'uom'
    ];

    public function processSchedule()
    {
        return $this->belongsTo(ProcessSchedule::class);
    }

    public function productionService()
    {
        return $this->belongsTo(ProductionService::class, 'service_id');
    }
}
