<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class JobCardCuttingSizeRatio extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_card_entry_id', 'article_no', 'size', 'ratio',
        'qty_fs', 'qty_hs', 'total_qty'
    ];

    public function jobCardEntry()
    {
        return $this->belongsTo(JobCardEntry::class);
    }
}
