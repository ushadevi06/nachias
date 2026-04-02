<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DebitNoteCharge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'debit_note_id',
        'charge_id',
        'charge_name',
        'charge_amount',
        'tax_type',
    ];

    public function debitNote()
    {
        return $this->belongsTo(DebitNote::class);
    }
    
    protected $dates = ['deleted_at'];
}
