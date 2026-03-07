<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_no', 'subject', 'description', 'ticket_cat_id', 'priority', 
        'requester_id', 'department_id', 'operation_stage_id', 'assigned_to_id', 
        'due_date', 'status', 'attachment', 'remarks', 'resolution_details', 
        'resolved_date', 'created_by', 'updated_by'
    ];

    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'ticket_cat_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function operationStage()
    {
        return $this->belongsTo(OperationStage::class);
    }
}
