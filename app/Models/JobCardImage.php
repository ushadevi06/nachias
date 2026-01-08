<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCardImage extends Model
{
    use HasFactory;

    protected $fillable = ['job_card_entry_id', 'art_no', 'image'];

    public function jobCardEntry()
    {
        return $this->belongsTo(JobCardEntry::class);
    }
}
