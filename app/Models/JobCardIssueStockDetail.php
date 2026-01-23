<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCardIssueStockDetail extends Model
{
    protected $fillable = [
        'job_card_issue_item_id',
        'stock_entry_item_id',
        'qty',
    ];
}
