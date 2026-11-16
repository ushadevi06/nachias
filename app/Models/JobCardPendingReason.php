<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobCardPendingReason extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'job_card_pending_reasons';

    protected $fillable = [
        'job_card_id',
        'job_card_no',
        'production_receipt_id',
        'reason',
        'qty',
        'remarks',
        'created_by',
        'updated_by',
    ];

    public const REASONS = [
        'Damaged',
        'Alteration',
        'Spl. Order Dispatch',
        'QC Hold',
        'Other',
    ];

    public function jobCard()
    {
        return $this->belongsTo(JobCardEntry::class, 'job_card_id');
    }

    public function productionReceipt()
    {
        return $this->belongsTo(ProductionReceipt::class, 'production_receipt_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
