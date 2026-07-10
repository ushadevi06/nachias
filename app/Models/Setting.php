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
        'qr_code',
        'phone_number',
        'state_id',
        'city_id',
        'address',
        'zip_code',
        'cgst',
        'sgst',
        'igst',
        'pan_no',
        'gst_no',
        'cin_no',
        'toll_free_no',
        'working_days',
        'opening_time',
        'closing_time',
        'po_prefix',
        'purchase_invoice_prefix',
        'so_prefix',
        'bank_name',
        'branch_location',
        'account_no',
        'ifsc_code',
        'terms_and_conditions',
    ];

    protected $casts = [
        'cgst' => 'float',
        'sgst' => 'float',
        'igst' => 'float',
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
