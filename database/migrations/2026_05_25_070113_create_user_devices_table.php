<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_devices', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | PRIMARY KEY
            |--------------------------------------------------------------------------
            */

            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | USER RELATION
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | CORE DEVICE IDENTITY
            |--------------------------------------------------------------------------
            */

            $table->string('device_id');

            $table->string('fingerprint_hash')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | DEVICE INFORMATION
            |--------------------------------------------------------------------------
            */

            $table->string('device_name')
                ->nullable();

            $table->string('device_type')
                ->nullable();

            $table->string('browser')
                ->nullable();

            $table->string('browser_version')
                ->nullable();

            $table->string('platform')
                ->nullable();

            $table->string('platform_version')
                ->nullable();

            $table->string('app_version')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | NETWORK & LOCATION
            |--------------------------------------------------------------------------
            */

            $table->string('last_ip_address')
                ->nullable();

            $table->string('first_ip_address')
                ->nullable();

            $table->string('last_country')
                ->nullable();

            $table->string('last_city')
                ->nullable();

            $table->string('timezone')
                ->nullable();

            $table->text('user_agent')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | SECURITY & TRUST
            |--------------------------------------------------------------------------
            */

            $table->enum('trust_level', [
                'NEW',
                'TRUSTED',
                'SUSPICIOUS',
                'BLOCKED'
            ])->default('NEW');

            $table->boolean('is_verified')
                ->default(false);

            $table->boolean('is_active')
                ->default(true);

            $table->boolean('is_current_device')
                ->default(false);

            $table->boolean('is_vpn_detected')
                ->default(false);

            $table->boolean('is_proxy_detected')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | FAILED SECURITY ATTEMPTS
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('failed_attempts')
                ->default(0);

            $table->unsignedInteger('failed_otp_attempts')
                ->default(0);

            $table->unsignedInteger('suspicious_activity_count')
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | LOGIN ACTIVITY
            |--------------------------------------------------------------------------
            */

            $table->timestamp('first_login_at')
                ->nullable();

            $table->timestamp('last_login_at')
                ->nullable();

            $table->timestamp('last_active_at')
                ->nullable();

            $table->timestamp('verified_at')
                ->nullable();

            $table->timestamp('blocked_at')
                ->nullable();

            $table->timestamp('trust_expires_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | SESSION & TOKEN TRACKING
            |--------------------------------------------------------------------------
            */

            $table->uuid('current_token_id')
                ->nullable();

            $table->string('session_id')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | DEVICE STATUS
            |--------------------------------------------------------------------------
            */

            $table->text('block_reason')
                ->nullable();

            $table->text('notes')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | TIMESTAMPS
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | INDEXES
            |--------------------------------------------------------------------------
            */

            $table->index('user_id');

            $table->index('device_id');

            $table->index('fingerprint_hash');

            $table->index('trust_level');

            $table->index('is_verified');

            $table->index('is_active');

            $table->index('last_ip_address');

            $table->index('last_active_at');

            /*
            |--------------------------------------------------------------------------
            | UNIQUE SECURITY RULES
            |--------------------------------------------------------------------------
            |
            | Same user same device duplicate not allowed
            | Same user same fingerprint duplicate not allowed
            | Different users same device allowed
            |
            */

            $table->unique([
                'user_id',
                'device_id'
            ], 'unique_user_device');

            $table->unique([
                'user_id',
                'fingerprint_hash'
            ], 'unique_user_fingerprint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};
