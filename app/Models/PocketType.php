<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PocketType extends Model
{
    use SoftDeletes;

    protected $fillable = ['pocket_type_name', 'status', 'created_by', 'updated_by'];

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}
