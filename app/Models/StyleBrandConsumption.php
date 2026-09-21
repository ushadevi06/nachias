<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StyleBrandConsumption extends Model
{
    use HasFactory;

    protected $table = 'style_brand_consumptions';

    protected $fillable = [
        'style_id',
        'brand_id',
        'average_consumption',
    ];

    public function style()
    {
        return $this->belongsTo(Style::class, 'style_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}