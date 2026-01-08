<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BottomCut extends Model
{
    use SoftDeletes;

    protected $fillable = ['bottom_cut_name', 'status', 'created_by', 'updated_by'];

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}
