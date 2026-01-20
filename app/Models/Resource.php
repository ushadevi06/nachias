<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resource extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'resource_code',
        'resource_name',
        'service_provider_id',
        'status',
        'created_by',
        'updated_by',
    ];

    // Relationship: Resource belongs to Service Provider (Plant)
    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }
}
