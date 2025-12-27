<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'state_id',
        'city_name',
        'city_code',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $dates = ['deleted_at'];

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
