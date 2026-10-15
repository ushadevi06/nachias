<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportBooking extends Model
{
    use HasFactory;

    protected $table = 'transport_bookings';

    protected $fillable = [
        'sales_invoice_id',
        'customer_id',
        'box_number',
        'box_index',
        'total_boxes',
        'quantity',
        'preferred_transport',
        'booking_office',
        'booking_rate',
        'delivery_agent_id',
        'delivery_agent_name',
        'picked_by_agent',
        'picked_at',
        'lr_no',
        'lr_price',
        'booking_date',
        'booking_time',
        'status',
        'payment_status',
        'payment_cleared_date',
        'customer_notified',
        'notified_at',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'picked_by_agent' => 'boolean',
        'picked_at' => 'datetime',
        'booking_date' => 'date',
        'payment_cleared_date' => 'date',
        'customer_notified' => 'boolean',
        'notified_at' => 'datetime',
        'booking_rate' => 'decimal:2',
        'lr_price' => 'decimal:2',
    ];

    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'sales_invoice_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function deliveryAgent()
    {
        return $this->belongsTo(User::class, 'delivery_agent_id');
    }
}
