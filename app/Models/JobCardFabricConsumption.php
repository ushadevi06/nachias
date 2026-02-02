<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCardFabricConsumption extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_card_fabric_detail_id',
        'size',
        'fs_cons',
        'hs_cons'
    ];

    public function fabricDetail()
    {
        return $this->belongsTo(JobCardFabricDetail::class, 'job_card_fabric_detail_id');
    }
}
