<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'store_type_name',
        'status',
        'created_by',
        'updated_by',
    ];
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}

