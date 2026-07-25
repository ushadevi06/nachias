<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRepository extends Model
{
    use HasFactory;
    protected $fillable = [
        'document_name',
        'reference_no',
        'document_type',
        'department_id',
        'validity_date',
        'status',
        'remarks',
        'file',
        'created_by',
        'updated_by',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
