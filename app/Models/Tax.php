<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    use SoftDeletes;

    protected $fillable = ['item_name', 'tax_rate', 'status'];
    public function getTaxRateFormattedAttribute()
    {
        return $this->tax_rate . '%';
    }
}


