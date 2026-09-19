<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationStageTarget extends Model
{
    use HasFactory;

    protected $table = 'operation_stage_targets';

    protected $fillable = [
        'operation_stage_id',
        'service_provider_id',
        'target_qty',
    ];

    public function operationStage()
    {
        return $this->belongsTo(OperationStage::class, 'operation_stage_id');
    }

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class, 'service_provider_id');
    }
}
