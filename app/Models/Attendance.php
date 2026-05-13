<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'emp_code',
        'date',
        'in_time',
        'out_time',
        'work_hours',
        'status',
        'is_manual',
        'updated_by',
    ];
}
