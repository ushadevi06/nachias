<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class JobCardArticleMatrix extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_card_entry_id', 'art_no', 
        'width', 'mtr', 'in_out', 'n_patti',
        'fs_36', 'fs_38', 'fs_40', 'fs_42', 'fs_44', 
        'hs_38', 'hs_40', 'hs_42', 'hs_44', 'hs_46',
        'ex_1', 'ex_2', 'row_total'
    ];

    public function jobCardEntry()
    {
        return $this->belongsTo(JobCardEntry::class);
    }
}
