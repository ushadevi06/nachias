<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;

    protected $fillable = ['department', 'status'];
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
