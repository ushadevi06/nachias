<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrnEntryItemVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'grn_entry_item_id',
        'color_id',
        'qty_received',
    ];
    public function grnEntryItem()
    {
        return $this->belongsTo(GrnEntryItem::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}
