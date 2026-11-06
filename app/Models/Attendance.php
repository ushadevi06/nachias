<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'emp_code',
        'device_serial_number',
        'date',
        'in_time',
        'out_time',
        'work_hours',
        'permission_hours',
        'status',
        'ot_status',
        'is_manual',
        'updated_by',
        'manual_updated_at',
        'reason',
    ];
}
