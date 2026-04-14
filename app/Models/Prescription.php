<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = [
        'user_id',
        'file_path',
        'notes',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    // 🔥 ADD THIS (IMPORTANT)
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}