<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class Employee extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'role_id',
        'emp_id',
        'name',
        'email',
        'phone',
        'date_of_joining',
        'blood_group_id',
        'father_name',
        'father_phone',
        'profile_image',
        'password',
        'status',

        // Address
        'country',
        'state_id',
        'city_id',
        'address_line1',
        'address_line2',
        'zipcode',

        // Emergency Contact
        'contact_person_name',
        'contact_person_phone',

        // Salary
        'basic_salary',
        'hra',
        'allowances',
        'deductions',
        'gross_salary',
        'net_salary',

        // Documents
        'esi_document',
        'pf_document',
        'aadhaar_document',
        'pan_document',

        // Bank
        'account_number',
        'bank_name',
        'ifsc_code',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'date_of_joining' => 'date',
        'basic_salary'    => 'decimal:2',
        'hra'             => 'decimal:2',
        'allowances'      => 'decimal:2',
        'deductions'      => 'decimal:2',
        'gross_salary'    => 'decimal:2',
        'net_salary'      => 'decimal:2',
    ];

    /* ===========================
        RELATIONSHIPS
    ============================ */

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function bloodGroup()
    {
        return $this->belongsTo(BloodGroup::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /* ===========================
        ACCESSORS
    ============================ */

    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_image) {
            return asset('uploads/employee/' . $this->id . '/' . $this->profile_image);
        }

        return asset('assets/img/default-user.png');
    }

    public function getEsiDocumentUrlAttribute()
    {
        return $this->esi_document
            ? asset('uploads/employee/' . $this->id . '/' . $this->esi_document)
            : null;
    }

    public function getPfDocumentUrlAttribute()
    {
        return $this->pf_document
            ? asset('uploads/employee/' . $this->id . '/' . $this->pf_document)
            : null;
    }

    public function getAadhaarDocumentUrlAttribute()
    {
        return $this->aadhaar_document
            ? asset('uploads/employee/' . $this->id . '/' . $this->aadhaar_document)
            : null;
    }

    public function getPanDocumentUrlAttribute()
    {
        return $this->pan_document
            ? asset('uploads/employee/' . $this->id . '/' . $this->pan_document)
            : null;
    }
    public static function generateEmployeeId()
    {
        $lastEmployee = self::orderBy('id', 'desc')->first();

        if (!$lastEmployee || !$lastEmployee->emp_id) {
            return 'EMP001';
        }

        $lastNumber = (int) str_replace('EMP', '', $lastEmployee->emp_id);
        $newNumber  = $lastNumber + 1;

        return 'EMP' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
