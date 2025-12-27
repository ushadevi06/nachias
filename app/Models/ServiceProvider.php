<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceProvider extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_type_id',
        'name',
        'code',
        'email',
        'mobile_no',
        'zip_code',
        'website_url',
        'service_rate',
        'status',
        'state_id',
        'city_id',
        'place_id',
        'address_line_1',
        'address_line_2',
        'contact_person_name',
        'designation',
        'phone_number',
        'contact_email',
        'pan_no',
        'gst_no',
        'remarks',
        'bank_name',
        'bank_acc_no',
        'ifsc_code',
        'payment_terms',
        'created_by',
        'updated_by',
    ];

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

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
}
