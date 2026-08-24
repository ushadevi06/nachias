<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'inv_no',
        'brand_id',
        'inv_date',
        'so_id',
        'so_ids',
        'customer_id',
        'store_id',
        'agent_id',
        'delivery_address',
        'remarks',
        'invoice_status',
        'delivery_status',
        'dispatch_completed_at',
        'payment_mode',
        'extra_input',
        'due_date',
        'notes',
        'signature_file',
        'attachment_file',
        'show_fields',
        'delivery_show_fields',
        'sub_total',
        'sales_discount',
        'box_discount_amount',
        'discount_percent',
        'discount',
        'transporter_name',
        'tran_doc_no',
        'tran_doc_date',
        'veh_type',
        'lr_no',
        'no_of_box',
        'hsn_sac',
        'commission_percent',
        'commission_amount',
        'total',
        'other_state',
        'tax_amount',
        'igst_percent',
        'igst',
        'cgst_percent',
        'cgst',
        'sgst_percent',
        'sgst',
        'other_charges',
        // 'pre_gst_charges',
        // 'post_gst_charges',
        'round_off_type',
        'round_off',
        'grand_total',
        'received_amount',
        'due_amount',
        'created_by',
        'updated_by',
        'irn',
        'ack_no',
        'ack_date',
        'signed_qr_code',
        'eway_bill_no',
        'eway_bill_date',
        'eway_bill_valid_till',
        'vehicle_no',
        'transporter_id',
        'transport_mode',
        'transport_distance',
        'qr_details',
    ];

    protected $casts = [
        'inv_date' => 'date',
        'due_date' => 'date',
        'other_state' => 'boolean',
        'show_fields' => 'array',
        'delivery_show_fields' => 'array',
        'ack_date' => 'datetime',
        'eway_bill_date' => 'datetime',
        'eway_bill_valid_till' => 'datetime',
        'dispatch_completed_at' => 'datetime',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class, 'so_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function items()
    {
        return $this->hasMany(SalesInvoiceItem::class, 'sales_invoice_id');
    }

    public static function cleanAddress($address)
    {
        if (empty($address)) {
            return '';
        }
        
        $lines = preg_split('/\r\n|\r|\n/', $address);
        $cleanedLines = [];
        $seenWords = [];
        
        foreach ($lines as $line) {
            $parts = explode(',', $line);
            $cleanedParts = [];
            
            foreach ($parts as $part) {
                $part = trim($part);
                if ($part === '') {
                    continue;
                }
                
                $lowerPart = strtolower($part);
                
                if (in_array($lowerPart, $seenWords)) {
                    continue;
                }
                
                $alreadyContained = false;
                if (preg_match('/^[a-zA-Z0-9]+$/', $part)) {
                    foreach ($seenWords as $seen) {
                        if (preg_match('/\b' . preg_quote($lowerPart, '/') . '\b/', $seen)) {
                            $alreadyContained = true;
                            break;
                        }
                    }
                }
                
                if ($alreadyContained) {
                    continue;
                }
                
                $cleanedParts[] = $part;
                $seenWords[] = $lowerPart;
            }
            
            if (!empty($cleanedParts)) {
                $cleanedLines[] = implode(', ', $cleanedParts);
            }
        }
        
        return implode("\n", $cleanedLines);
    }
    public function store()
    {
        return $this->belongsTo(StoreType::class, 'store_id');
    }
}
