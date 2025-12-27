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

    // Accessor to get size array
    public function getSizeArrayAttribute()
    {
        return explode(',', $this->size);
    }

    // Accessor to get ratio array
    public function getRatioArrayAttribute()
    {
        return explode(',', $this->ratio);
    }

    // Validation for equal count of size and ratio
    public static function validateSizeRatio($size, $ratio)
    {
        $sizeCount = count(explode(',', $size));
        $ratioCount = count(explode(',', $ratio));

        return $sizeCount === $ratioCount;
    }
}
