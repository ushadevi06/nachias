<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class JobCardFabricDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_card_entry_id', 'art_no', 
        'width', 'mtr', 'in_out', 'n_patti', 'row_total',
        'fs_qty', 'hs_qty', 'total_qty', 'used_qty', 'remaining_qty'
    ];

    public function jobCardEntry()
    {
        return $this->belongsTo(JobCardEntry::class);
    }

    public function quantities()
    {
        return $this->hasMany(JobCardMatrixQuantity::class, 'job_card_fabric_detail_id');
    }

    public function consumptions()
    {
        return $this->hasMany(JobCardFabricConsumption::class, 'job_card_fabric_detail_id');
    }
}
