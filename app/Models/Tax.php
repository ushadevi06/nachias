<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    use SoftDeletes;

    protected $fillable = ['item_name', 'tax_rate', 'status','created_by','updated_by'];
    public function getTaxRateFormattedAttribute()
    {
        return $this->tax_rate . '%';
    }
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}


