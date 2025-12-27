<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseCommissionAgent extends Model
{
    use SoftDeletes;
    protected $table = 'purchase_commission_agents';

    protected $fillable = [
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
        'zipcode',
        'contact_person_name',
        'designation',
        'phone_number',
        'contact_email',
        'pan_no',
        'gst_no',
        'remarks',
        'created_by',
        'updated_by',
    ];

    // ------------------------
    // Relationships
    // ------------------------

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function servicePoint()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
