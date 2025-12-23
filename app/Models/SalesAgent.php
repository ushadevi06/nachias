<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesAgent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'agent_type',
        'name',
        'code',
        'email',
        'mobile_no',
        'status',
        'state_id',
        'city_id',
        'place_id',
        'address_line_1',
        'address_line_2',
        'zip_code',
        'contact_person_name',
        'designation',
        'contact_phone_number',
        'contact_email',
        'pan_no',
        'gst_no',
        'commission_value',
        'sales_target'
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}
