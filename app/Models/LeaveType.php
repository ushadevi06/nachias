<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'leave_types';

    protected $fillable = [
        'leave_type',
        'status',
        'created_by',
        'updated_by'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    protected $dates = ['deleted_at'];
}

