<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UserDevice extends Model
{
    use HasUuids;

    /*
    |--------------------------------------------------------------------------
    | TRUST LEVELS
    |--------------------------------------------------------------------------
    */

    const TRUST_NEW = 'NEW';

    const TRUST_TRUSTED = 'TRUSTED';

    const TRUST_BLOCKED = 'BLOCKED';

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    protected $table = 'user_devices';

    public $incrementing = false;

    protected $keyType = 'string';

    /*
    |--------------------------------------------------------------------------
    | FILLABLE
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'user_id',
        'device_id',
        'fingerprint_hash',
        'device_name',
        'browser',
        'platform',
        'app_version',
        'last_ip_address',
        'last_country',
        'last_city',
        'user_agent',
        'trust_level',
        'failed_attempts',
        'last_active_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */

    protected $casts = [

        'last_active_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}