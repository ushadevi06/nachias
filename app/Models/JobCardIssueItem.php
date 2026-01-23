<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobCardIssueItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_card_entry_id',
        'job_card_article_matrix_id',
        'stock_entry_item_id',
        'qty_issue',
        'qty_adjusted',
        'qty_wastage',
        'qty_used',
        'bit',
        'balance',
        'average',
        'produced_qty',
        'unit_price',
        'total_cost',
        'cost_per_pc',
        'stock_entry_item_id',
        'sleeve_type',
        'created_by',
        'updated_by',
    ];

    public function jobCard()
    {
        return $this->belongsTo(JobCardEntry::class, 'job_card_entry_id');
    }

    public function fabricDetail()
    {
        return $this->belongsTo(JobCardFabricDetail::class, 'job_card_article_matrix_id');
    }

    public function stockDetails()
    {
        return $this->hasMany(JobCardIssueStockDetail::class);
    }
}
