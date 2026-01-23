<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskReceive extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'task_receives';

    protected $fillable = [
        'task_receive_no',
        'task_id',
        'received_services',
        'received_date',
        'received_from',
        'received_store',
        'good_qty',
        'rework_qty',
        'wastage_qty',
        'qc_status',
        'remarks',
        'shift_id',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'received_services' => 'array'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function receivedFrom()
    {
        return $this->belongsTo(User::class, 'received_from');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }
}
