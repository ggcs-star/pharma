<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SecurityAuditLog extends Model
{
    use HasUuids;

    protected $table = 'security_audit_logs';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [

        'user_id',
        'user_device_id',
        'device_id',

        'event_category',
        'action_type',

        'request_method',
        'api_endpoint',
        'route_name',
        'full_url',

        'ip_address',
        'country',
        'city',

        'user_agent',

        'severity_score',
        'response_status',

        'request_id',
        'session_trace_id',
        'token_id',

        'is_suspicious',
        'is_blocked',
        'requires_admin_review',

        'failure_reason',
        'exception_message',

        'metadata',
    ];

    protected $casts = [

        'metadata' => 'array',

        'is_suspicious' => 'boolean',
        'is_blocked' => 'boolean',
        'requires_admin_review' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}