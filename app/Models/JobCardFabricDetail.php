<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class JobCardFabricDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_card_entry_id', 'is_additional', 'additional_batch_no', 'art_no', 'stock_entry_id',
        'width', 'mtr', 'stock_total_qty', 'in_out', 'n_patti', 'row_total',
        'fs_qty', 'hs_qty', 'total_qty', 'used_qty', 'remaining_qty', 'grn_image'
    ];

    public function jobCardEntry()
    {
        return $this->belongsTo(JobCardEntry::class);
    }

    public function stockEntry()
    {
        return $this->belongsTo(StockEntry::class, 'stock_entry_id');
    }

    public function rawMaterial()
    {
        return $this->hasOneThrough(
            RawMaterial::class,
            StockEntryItem::class,
            'id', 
            'id', 
            'stock_entry_id', 
            'raw_material_id'
        );
    }

    public function fabricSize()
    {
        return $this->belongsTo(FabricSize::class, 'width');
    }

    public function quantities()
    {
        return $this->hasMany(JobCardMatrixQuantity::class, 'job_card_fabric_detail_id');
    }

    public function consumptions()
    {
        return $this->hasMany(JobCardFabricConsumption::class, 'job_card_fabric_detail_id');
    }

    public function layMarks()
    {
        return $this->hasMany(JobCardLayMark::class, 'job_card_fabric_detail_id');
    }

    public function productionReceipts()
    {
        return $this->hasMany(ProductionReceipt::class, 'job_card_fabric_detail_id');
    }

    public function isPostedToWarehouse(): bool
    {
        return $this->productionReceipts()->where('status', 'Posted')->exists();
    }

    public function getBatchTotalQtyAttribute(): float
    {
        if ($this->is_additional && !empty($this->additional_batch_no)) {
            return (float) static::where('job_card_entry_id', $this->job_card_entry_id)
                ->where('is_additional', 1)
                ->where('additional_batch_no', $this->additional_batch_no)
                ->sum('total_qty');
        }
        return (float) ($this->total_qty ?? 0);
    }

    public function getBatchFsQtyAttribute(): float
    {
        if ($this->is_additional && !empty($this->additional_batch_no)) {
            return (float) static::where('job_card_entry_id', $this->job_card_entry_id)
                ->where('is_additional', 1)
                ->where('additional_batch_no', $this->additional_batch_no)
                ->sum('fs_qty');
        }
        return (float) ($this->fs_qty ?? 0);
    }

    public function getBatchHsQtyAttribute(): float
    {
        if ($this->is_additional && !empty($this->additional_batch_no)) {
            return (float) static::where('job_card_entry_id', $this->job_card_entry_id)
                ->where('is_additional', 1)
                ->where('additional_batch_no', $this->additional_batch_no)
                ->sum('hs_qty');
        }
        return (float) ($this->hs_qty ?? 0);
    }
}
