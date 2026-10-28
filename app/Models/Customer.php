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
        'tamil_name',
        'tamil_address',
        'tamil_place',
        'code',
        'mobile_no',
        'email',
        'website_url',
        'transport_name',
        'alt_transport_name',
        'booking_office',
        'zone_id',
        'store_id',
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
        'box_discount_amount',
        // 'transport_rate',
        'bank_name',
        'branch',
        'account_number',
        'ifsc_code',
        'created_by',
        'updated_by',
    ];

    public function storeType()
    {
        return $this->belongsTo(StoreType::class, 'store_id');
    }

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

    public function getContactInfoAttribute()
    {
        return [
            'email' => $this->email,
            'mobile' => $this->mobile_no
        ];
    }
    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class, 'customer_id');
    }

    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class, 'customer_id');
    }

    public function openingBalance()
    {
        return $this->hasOne(CustomerOpeningBalance::class, 'customer_id');
    }

    public function scopeActive($query){
        return $query->where('status', 'Active');
    }

    public function getCreditLimitStatus($additionalAmount = 0)
    {
        $creditLimit = (float)($this->credit_limit ?? 0);
        if ($creditLimit <= 0) {
            return [
                'has_limit'             => false,
                'exceeded'              => false,
                'credit_limit'          => 0,
                'current_outstanding'   => 0,
                'projected_outstanding' => 0,
                'available_limit'       => 0,
                'exceeded_by'           => 0,
            ];
        }

        $hasOpenTable = \Illuminate\Support\Facades\Schema::hasTable('customer_opening_balances');
        $openAmount = ($hasOpenTable && class_exists(\App\Models\CustomerOpeningBalance::class))
            ? (float)(\App\Models\CustomerOpeningBalance::where('customer_id', $this->id)->orWhere('customer_code', $this->code)->value('open_amount') ?? 0)
            : 0;

        $invoicesQuery = \App\Models\SalesInvoice::where('customer_id', $this->id)
            ->whereNull('deleted_at')
            ->where(function($q) {
                $q->whereNull('einvoice_status')
                  ->orWhereRaw('LOWER(einvoice_status) != ?', ['cancelled']);
            });

        $totalSales = (float)(clone $invoicesQuery)->sum('grand_total');
        $totalReceived = (float)(clone $invoicesQuery)->sum('received_amount');

        $currentOutstanding = max(0, $openAmount + $totalSales - $totalReceived);
        $projected = $currentOutstanding + (float)$additionalAmount;
        $exceeded = $projected > $creditLimit;

        return [
            'has_limit'             => true,
            'exceeded'              => $exceeded,
            'credit_limit'          => $creditLimit,
            'current_outstanding'   => $currentOutstanding,
            'projected_outstanding' => $projected,
            'available_limit'       => max(0, $creditLimit - $currentOutstanding),
            'exceeded_by'           => $exceeded ? ($projected - $creditLimit) : 0,
        ];
    }
}