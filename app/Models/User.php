<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasRoles, Notifiable;

    protected $fillable = [
    'name',
    'email',
    'password',
    'google_id',
    'email_verified_at',
    'profile_image',
    'mobile',
    'address',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    // 🔥 ADD THIS
public function purchaseOrders()
{
    return $this->hasMany(
        \App\Models\Purchase\PurchaseOrder::class,
        'retailer_id'
    );
}

    public function getProfileImageAttribute($value)
    {
        if (!$value) {
            return null;
        }
        

        if (str_starts_with($value, 'http')) {
            return $value;
        }

        return Storage::disk('s3')->url($value);
    }
        
}
