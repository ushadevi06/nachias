<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCardSleeveMeter extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_card_id',
        'sleeve_type',
        'meter',
    ];

    public function jobCard()
    {
        return $this->belongsTo(JobCardEntry::class, 'job_card_id');
    }
}
