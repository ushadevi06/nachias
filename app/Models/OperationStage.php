<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperationStage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'operation_stage_name',
        'status',
        'created_by',
        'updated_by',
    ];
    protected $dates = ['deleted_at'];
}
