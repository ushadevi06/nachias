<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldSalesInvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'old_sales_invoice_items';

    protected $fillable = [
        'old_sales_invoice_id',
        'doc_no',
        'description',
        'brand_id',
        'style_id',
        'size',
        'class2_sleeve',
        'hsn_sac',
        'mrp',
        'price',
        'quantity',
        'gross_amount',
    ];

    protected $casts = [
        'mrp' => 'decimal:2',
        'price' => 'decimal:2',
        'quantity' => 'decimal:2',
        'gross_amount' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(OldSalesInvoice::class, 'old_sales_invoice_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function style()
    {
        return $this->belongsTo(Style::class, 'style_id');
    }
}
