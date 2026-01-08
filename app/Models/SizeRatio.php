<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SizeRatio extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'size',
        'ratio',
        'status',
        'created_by',
        'updated_by',
    ];

    public function getSizeArrayAttribute()
    {
        return explode(',', $this->size);
    }

    public function getRatioArrayAttribute()
    {
        return explode(',', $this->ratio);
    }

    public function getDisplayAttribute()
    {
        return "({$this->size}) - ({$this->ratio})";
    }

    public static function validateSizeRatio($size, $ratio)
    {
        $sizeCount = count(explode(',', $size));
        $ratioCount = count(explode(',', $ratio));

        return $sizeCount === $ratioCount;
    }
}
