<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreCategory extends Model
{
    use SoftDeletes;

    protected $table = 'store_categories';

    protected $fillable = [
        'code',
        'category_name',
        'description',
        'status',
        'created_by',
    ];
    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'brand_store_categories', 'store_category_id', 'brand_id')->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
    protected $dates = ['deleted_at'];
}
