<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_name',
        'email',
        'logo',
        'phone_number',
        'state_id',
        'city_id',
        'address',
        'cgst',
        'sgst',
        'igst',
        'pan_no',
        'gst_no',
        'cin_no',
    ];

    protected $casts = [
        'cgst' => 'integer',
        'sgst' => 'integer',
        'igst' => 'integer',
    ];

    /**
     * Get the state that owns the setting.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the city that owns the setting.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
