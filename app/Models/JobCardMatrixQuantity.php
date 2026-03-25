<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobCardMatrixQuantity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_card_fabric_detail_id', 'size', 'color', 'color_id', 'qty_fs', 'qty_hs', 'total_qty'
    ];

    public function fabricDetail()
    {
        return $this->belongsTo(JobCardFabricDetail::class, 'job_card_fabric_detail_id');
    }

    public function colorRel()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }
}
