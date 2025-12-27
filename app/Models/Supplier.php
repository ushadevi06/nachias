<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'mobile_no',
        'email',
        'website_url',
        'transport_name',
        'booking_area',
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

        'purchase_commission_agent_id',
        'commission_percentage',

        'gst_no',
        'tax_id',
        'pan_no',
        'ecc_no',

        'credit_limit',
        'payment_terms',

        'bank_name',
        'branch',
        'account_number',
        'ifsc_code',
        'created_by',
        'updated_by',
    ];
    public function state()
    {
        return $this->belongsTo(\App\Models\State::class);
    }

    public function city()
    {
        return $this->belongsTo(\App\Models\City::class);
    }

    public function place()
    {
        return $this->belongsTo(\App\Models\Place::class);
    }

    public function purchaseCommissionAgent()
    {
        return $this->belongsTo(\App\Models\PurchaseCommissionAgent::class, 'purchase_commission_agent_id');
    }
    public function tax()
    {
        return $this->belongsTo(\App\Models\Tax::class, 'tax_id');
    }
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}
