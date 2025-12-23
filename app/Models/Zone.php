<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zone extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'zone_name',
        'state_id',
        'city_ids',
        'status'
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    // Helper method to get city names
    public function getCityNamesAttribute()
    {
        if (!$this->city_ids) return '';

        $cityIds = explode(',', $this->city_ids);
        $cities = City::whereIn('id', $cityIds)->pluck('city_name')->toArray();
        return implode(', ', $cities);
    }
}
