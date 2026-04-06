<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'name',
        'contact',
        'email',
        'registration_number',
        'professional_credential',
        'medical_speciality',
        'clinic_name',
        'clinic_city',
        'clinic_pincode',
        'clinic_address'
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}