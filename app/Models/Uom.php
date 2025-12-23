<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Uom extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uom_code',
        'uom_name',
        'description',
        'status'
    ];
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
    protected $dates = ['deleted_at'];
}
