<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCardLayMark extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_card_fabric_detail_id',
        'mark_no',
        'sizes',
        'sleeve_type',
        'lay_mark_meter',
        'no_of_lay'
    ];

    protected $casts = [
        'sizes' => 'array',
    ];

    public function fabricDetail()
    {
        return $this->belongsTo(JobCardFabricDetail::class, 'job_card_fabric_detail_id');
    }
}
