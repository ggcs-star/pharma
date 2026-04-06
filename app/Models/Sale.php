<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Sale extends Model
{
    protected $fillable = [
        'customer_id',
        'doctor_id',
        'bill_number',
        'bill_date',
        'entry_by',
        'total_amount',
        'gst',
        'discount',
        'net_amount',
        'sales_type',
        'patient_name',
        'patient_contact'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function items()
    {
        return $this->hasMany(SalesItem::class);
    }

    // ✅ ADD THIS
    public function user()
    {
        return $this->belongsTo(User::class, 'entry_by');
    }
}