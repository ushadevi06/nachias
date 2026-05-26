<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditNoteCharge extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'credit_note_charges';

    protected $fillable = [
        'credit_note_id',
        'charge_id',
        'charge_name',
        'charge_amount',
        'tax_type',
    ];

    public function creditNote()
    {
        return $this->belongsTo(CreditNote::class);
    }

    public function charge()
    {
        return $this->belongsTo(Charge::class);
    }
}
