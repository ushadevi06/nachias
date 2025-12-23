<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category',
        'name',
        'code',
        'mobile_no',
        'email',
        'website_url',
        'transport_name',
        'booking_office',
        'zone_id',
        'stores',
        'status',
        'state_id',
        'city_id',
        'place_id',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'zip_code',
        'contact_person_name',
        'designation',
        'contact_mobile_no',
        'contact_email',
        'tax_type_id',
        'gst_no',
        'pan_no',
        'payment_terms',
        'credit_limit',
        'sales_discount',
        'box_discount',
        'bank_name',
        'branch',
        'account_number',
        'ifsc_code'
    ];

    // Relationships
    public function zone()
    {
        return $this->belongsTo(Zone::class);
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

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_type_id');
    }

    // Accessor for formatted contact info
    public function getContactInfoAttribute()
    {
        return [
            'email' => $this->email,
            'mobile' => $this->mobile_no
        ];
    }
}