<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BrandCategory extends Model
{
    use SoftDeletes;

    protected $table = 'brand_categories';

    protected $fillable = [
        'code',
        'name',
        'description',
        'status',
        'created_by',
    ];

    protected $dates = ['deleted_at'];

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
