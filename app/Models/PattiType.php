<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PattiType extends Model
{
    use SoftDeletes;

    protected $fillable = ['patti_type_name', 'status', 'created_by', 'updated_by'];

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}
