<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockConsumableIssue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'issue_no',
        'issue_date',
        'issue_type',
        'production_stage',
        'remarks',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(StockConsumableIssueItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
