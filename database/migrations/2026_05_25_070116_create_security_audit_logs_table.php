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
        Schema::create('security_audit_logs', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | PRIMARY KEY
            |--------------------------------------------------------------------------
            */

            $table->uuid('id')->primary();

            /*
            |--------------------------------------------------------------------------
            | USER & DEVICE RELATIONS
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->uuid('user_device_id')
                ->nullable();

            $table->string('device_id')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | SECURITY EVENT TRACKING
            |--------------------------------------------------------------------------
            */

            $table->string('event_category');

            $table->string('action_type');

            $table->string('event_source')
                ->nullable();

            $table->string('auth_guard')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | REQUEST INFORMATION
            |--------------------------------------------------------------------------
            */

            $table->string('request_method')
                ->nullable();

            $table->string('api_endpoint')
                ->nullable();

            $table->string('route_name')
                ->nullable();

            $table->text('full_url')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | NETWORK INFORMATION
            |--------------------------------------------------------------------------
            */

            $table->string('ip_address')
                ->nullable();

            $table->string('country')
                ->nullable();

            $table->string('city')
                ->nullable();

            $table->text('user_agent')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | SECURITY ANALYTICS
            |--------------------------------------------------------------------------
            */

            $table->unsignedTinyInteger('severity_score')
                ->default(1);

            $table->unsignedSmallInteger('response_status')
                ->nullable();

            $table->boolean('is_suspicious')
                ->default(false);

            $table->boolean('is_blocked')
                ->default(false);

            $table->boolean('requires_admin_review')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | TRACEABILITY
            |--------------------------------------------------------------------------
            */

            $table->uuid('request_id')
                ->nullable();

            $table->uuid('session_trace_id')
                ->nullable();

            $table->uuid('token_id')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | FAILURE & ERROR TRACKING
            |--------------------------------------------------------------------------
            */

            $table->string('failure_reason')
                ->nullable();

            $table->text('exception_message')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | METADATA
            |--------------------------------------------------------------------------
            */

            $table->json('metadata')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | TIMESTAMPS
            |--------------------------------------------------------------------------
            */

            $table->timestamp('created_at')
                ->useCurrent();

            /*
            |--------------------------------------------------------------------------
            | INDEXES
            |--------------------------------------------------------------------------
            */

            $table->index('user_id');

            $table->index('device_id');

            $table->index('event_category');

            $table->index('action_type');

            $table->index('request_id');

            $table->index('severity_score');

            $table->index('response_status');

            $table->index('is_suspicious');

            $table->index('requires_admin_review');

            $table->index([
                'user_id',
                'created_at'
            ]);

            $table->index([
                'event_category',
                'created_at'
            ]);

            $table->index([
                'severity_score',
                'created_at'
            ]);

            /*
            |--------------------------------------------------------------------------
            | FOREIGN KEYS
            |--------------------------------------------------------------------------
            */

            $table->foreign('user_device_id')
                ->references('id')
                ->on('user_devices')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_audit_logs');
    }
};
