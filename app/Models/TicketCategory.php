<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['category_name', 'status'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'ticket_cat_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}
