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
        'checking_status',
        'checked_by',
        'checked_at',
        'kachathu_status',
        'kachathu_by',
        'kachathu_at',
        'kachathu_details',
        'ready_for_dispatch',
        'ready_for_dispatch_at',
        'transport_sticker_printed',
        'transport_sticker_printed_at',
        'sent_to_accounts',
        'sent_to_accounts_by',
        'sent_to_accounts_at',
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
        if (empty($address) || stripos($address, 'Fetching delivery address') !== false) {
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

    public static function isRawMaterialOrScrapStore($storeIdOrModel, $storeName = null)
    {
        if (!empty($storeName)) {
            $name = strtoupper(trim($storeName));
            if (str_contains($name, 'FABRIC') || str_contains($name, 'ACCESSORIES') || str_contains($name, 'SCRAP')) {
                return true;
            }
        }

        if ($storeIdOrModel instanceof StoreType) {
            $name = strtoupper(trim($storeIdOrModel->store_type_name ?? ''));
            $id = $storeIdOrModel->id;
        } elseif (is_numeric($storeIdOrModel)) {
            $id = (int)$storeIdOrModel;
            $name = strtoupper(trim(StoreType::where('id', $id)->value('store_type_name') ?? ''));
        } elseif (is_string($storeIdOrModel)) {
            $name = strtoupper(trim($storeIdOrModel));
            $id = null;
        } else {
            return false;
        }

        return str_contains($name, 'FABRIC')
            || str_contains($name, 'ACCESSORIES')
            || str_contains($name, 'SCRAP')
            || in_array($id, [1, 2]);
    }

    public static function isScrapStore($storeIdOrModel, $storeName = null)
    {
        if (!empty($storeName)) {
            $name = strtoupper(trim($storeName));
            if (str_contains($name, 'SCRAP')) {
                return true;
            }
        }

        if ($storeIdOrModel instanceof StoreType) {
            $name = strtoupper(trim($storeIdOrModel->store_type_name ?? ''));
        } elseif (is_numeric($storeIdOrModel)) {
            $name = strtoupper(trim(StoreType::where('id', $storeIdOrModel)->value('store_type_name') ?? ''));
        } elseif (is_string($storeIdOrModel)) {
            $name = strtoupper(trim($storeIdOrModel));
        } else {
            return false;
        }

        return str_contains($name, 'SCRAP');
    }

    public function isRawMaterialOrScrap()
    {
        return self::isRawMaterialOrScrapStore($this->store ?? $this->store_id);
    }

    public function isScrap()
    {
        return self::isScrapStore($this->store ?? $this->store_id);
    }

    /**
     * Base sequence offset to map internal CDW sequence numbers to public CD sequence numbers.
     * Based on the last live CD invoice CD/313/26-27.
     */
    const CDW_CD_OFFSET = 313;

    /**
     * Convert DB invoice number (e.g. CDW/1/26-27) to display/E-invoice number (e.g. CD/314/26-27).
     * Since CDW/3 was converted to CF/3302, numbers >= 4 collapse the gap so sequence is fully continuous.
     */
    public static function formatDisplayInvNo($invNo)
    {
        if (empty($invNo)) {
            return $invNo;
        }

        if (preg_match('/^CDW\/(\d+)\/(.*)$/i', $invNo, $matches)) {
            $num = (int)$matches[1];
            $fy = $matches[2];
            $newNum = ($num >= 4) ? (self::CDW_CD_OFFSET + $num - 1) : (self::CDW_CD_OFFSET + $num);
            return "CD/{$newNum}/{$fy}";
        }

        return $invNo;
    }

    /**
     * Convert display/input invoice number (e.g. CD/314/26-27) back to DB format (e.g. CDW/1/26-27).
     * Numbers <= CDW_CD_OFFSET remain as CD.
     */
    public static function formatDbInvNo($invNo)
    {
        if (empty($invNo)) {
            return $invNo;
        }

        if (preg_match('/^CD\/(\d+)\/(.*)$/i', $invNo, $matches)) {
            $num = (int)$matches[1];
            $fy = $matches[2];
            if ($num >= 316) {
                $dbNum = $num - self::CDW_CD_OFFSET + 1;
                return "CDW/{$dbNum}/{$fy}";
            } elseif ($num > self::CDW_CD_OFFSET) {
                $dbNum = $num - self::CDW_CD_OFFSET;
                return "CDW/{$dbNum}/{$fy}";
            }
        }

        return $invNo;
    }

    /**
     * Accessor: Automatically formats inv_no for display, prints, and e-invoicing.
     */
    public function getInvNoAttribute($value)
    {
        return self::formatDisplayInvNo($value);
    }

    /**
     * Mutator: Ensures DB always stores the underlying CDW representation without altering DB constraints.
     */
    public function setInvNoAttribute($value)
    {
        $this->attributes['inv_no'] = self::formatDbInvNo($value);
    }

    public function checkedByUser()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function accountsSentByUser()
    {
        return $this->belongsTo(User::class, 'sent_to_accounts_by');
    }

    public function kachathuByUser()
    {
        return $this->belongsTo(User::class, 'kachathu_by');
    }

    public function transportBookings()
    {
        if (!class_exists(\App\Models\TransportBooking::class)) {
            return $this->hasMany(self::class, 'id')->whereRaw('1=0');
        }
        return $this->hasMany(\App\Models\TransportBooking::class, 'sales_invoice_id');
    }

    /**
     * Raw accessor to access DB value if explicitly needed.
     */
    public function getRawInvNoAttribute()
    {
        return $this->attributes['inv_no'] ?? null;
    }

    /**
     * Accessor: Automatically formats invoice numbers inside QR code details for stickers and scans.
     */
    public function getQrDetailsAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }

        return preg_replace_callback('/CDW\/(\d+)\/([^\s\r\n]+)/i', function ($matches) {
            $num = (int)$matches[1];
            $fy = $matches[2];
            $newNum = ($num >= 4) ? (self::CDW_CD_OFFSET + $num - 1) : (self::CDW_CD_OFFSET + $num);
            return "CD/{$newNum}/{$fy}";
        }, $value);
    }
}

