<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes;

    protected $table = 'brands';

    protected $fillable = [
        'brand_name',
        'code',
        'status',
        'created_by',
    ];

    protected $dates = ['deleted_at'];

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
