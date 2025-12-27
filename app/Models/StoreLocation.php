<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreLocation extends Model
{
    use SoftDeletes;

    protected $fillable = ['store_location', 'status','created_by','updated_by'];
}
