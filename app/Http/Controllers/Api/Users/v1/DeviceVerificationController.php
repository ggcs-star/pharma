<?php

namespace App\Http\Controllers\Api\Users\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use App\Mail\OtpMail;
use App\Models\UserDevice;
use App\Services\SecurityLogger;

class DeviceVerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SEND DEVICE VERIFICATION OTP
    |--------------------------------------------------------------------------
    */

    public function sendOtp(Request $request)
    {
        try {

            $user = auth()->user();

            /*
            |--------------------------------------------------------------------------
            | CURRENT DEVICE
            |--------------------------------------------------------------------------
            */

            $device = $request
                ->attributes
                ->get('current_device');

            if (!$device) {

                SecurityLogger::log(
                    $request,
                    'SECURITY',
                    'device_not_found',
                    8,
                    404
                );

                return response()->json([
                    'status' => false,
                    'message' => 'Current device not found'
                ], 404);
            }

            /*
            |--------------------------------------------------------------------------
            | BLOCKED DEVICE
            |--------------------------------------------------------------------------
            */

            if (
                $device->trust_level ===
                UserDevice::TRUST_BLOCKED
            ) {

                SecurityLogger::log(
                    $request,
                    'SECURITY',
                    'blocked_device_attempt',
                    10,
                    403,
                    [
                        'device_id' => $device->id
                    ]
                );

                return response()->json([
                    'status' => false,
                    'message' => 'This device is blocked'
                ], 403);
            }

            /*
            |--------------------------------------------------------------------------
            | ALREADY TRUSTED
            |--------------------------------------------------------------------------
            */

            if (
                $device->trust_level ===
                UserDevice::TRUST_TRUSTED
            ) {

                return response()->json([
                    'status' => true,
                    'message' => 'Device already trusted',
                    'trust_level' => $device->trust_level
                ], 200);
            }

            /*
            |--------------------------------------------------------------------------
            | GENERATE OTP
            |--------------------------------------------------------------------------
            */

            $otp = rand(100000, 999999);

            /*
            |--------------------------------------------------------------------------
            | CACHE KEY
            |--------------------------------------------------------------------------
            */

            $cacheKey =
                'device_verification_' .
                $user->id .
                '_' .
                $device->id;

            /*
            |--------------------------------------------------------------------------
            | STORE HASHED OTP
            |--------------------------------------------------------------------------
            */

            Cache::put(
                $cacheKey,
                Hash::make($otp),
                now()->addMinutes(10)
            );

            /*
            |--------------------------------------------------------------------------
            | SEND MAIL
            |--------------------------------------------------------------------------
            */

            Mail::to($user->email)
                ->send(new OtpMail($otp));

            /*
            |--------------------------------------------------------------------------
            | SECURITY LOG
            |--------------------------------------------------------------------------
            */

            SecurityLogger::log(
                $request,
                'SECURITY',
                'device_otp_sent',
                2,
                200,
                [
                    'user_id' => $user->id,
                    'device_id' => $device->id
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | SYSTEM LOG
            |--------------------------------------------------------------------------
            */

            Log::info('DEVICE OTP SENT', [
                'user_id' => $user->id,
                'device_id' => $device->id
            ]);

            /*
            |--------------------------------------------------------------------------
            | RESPONSE
            |--------------------------------------------------------------------------
            */

            return response()->json([
                'status' => true,
                'message' => 'Verification OTP sent successfully',
                'expires_in_seconds' => 600
            ], 200);

        } catch (\Throwable $e) {

            Log::error('SEND DEVICE OTP ERROR', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Unable to send OTP'
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | VERIFY DEVICE OTP
    |--------------------------------------------------------------------------
    */

    public function verifyOtp(Request $request)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | VALIDATION
            |--------------------------------------------------------------------------
            */

            $request->validate([
                'otp' => 'required|numeric|digits:6'
            ]);

            $user = auth()->user();

            /*
            |--------------------------------------------------------------------------
            | CURRENT DEVICE
            |--------------------------------------------------------------------------
            */

            $device = $request
                ->attributes
                ->get('current_device');

            if (!$device) {

                SecurityLogger::log(
                    $request,
                    'SECURITY',
                    'device_not_found',
                    8,
                    404
                );

                return response()->json([
                    'status' => false,
                    'message' => 'Current device not found'
                ], 404);
            }

            /*
            |--------------------------------------------------------------------------
            | BLOCKED DEVICE
            |--------------------------------------------------------------------------
            */

            if (
                $device->trust_level ===
                UserDevice::TRUST_BLOCKED
            ) {

                return response()->json([
                    'status' => false,
                    'message' => 'This device is blocked'
                ], 403);
            }

            /*
            |--------------------------------------------------------------------------
            | CACHE KEY
            |--------------------------------------------------------------------------
            */

            $cacheKey =
                'device_verification_' .
                $user->id .
                '_' .
                $device->id;

            /*
            |--------------------------------------------------------------------------
            | GET HASHED OTP
            |--------------------------------------------------------------------------
            */

            $cachedOtp = Cache::get($cacheKey);

            /*
            |--------------------------------------------------------------------------
            | OTP EXPIRED
            |--------------------------------------------------------------------------
            */

            if (!$cachedOtp) {

                SecurityLogger::log(
                    $request,
                    'SECURITY',
                    'device_otp_expired',
                    6,
                    422,
                    [
                        'device_id' => $device->id
                    ]
                );

                return response()->json([
                    'status' => false,
                    'message' => 'OTP expired'
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | INVALID OTP
            |--------------------------------------------------------------------------
            */

            if (
                !Hash::check(
                    $request->otp,
                    $cachedOtp
                )
            ) {

                /*
                |--------------------------------------------------------------------------
                | FAILED ATTEMPTS
                |--------------------------------------------------------------------------
                */

                $device->increment(
                    'failed_attempts'
                );

                $device->refresh();

                SecurityLogger::log(
                    $request,
                    'SECURITY',
                    'device_otp_failed',
                    8,
                    422,
                    [
                        'device_id' => $device->id,
                        'failed_attempts' =>
                            $device->failed_attempts
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | BLOCK DEVICE
                |--------------------------------------------------------------------------
                */

                if (
                    $device->failed_attempts >= 5
                ) {

                    $device->update([
                        'trust_level' =>
                            UserDevice::TRUST_BLOCKED
                    ]);

                    Cache::forget($cacheKey);

                    SecurityLogger::log(
                        $request,
                        'SECURITY',
                        'device_blocked',
                        10,
                        403,
                        [
                            'device_id' =>
                                $device->id
                        ]
                    );

                    return response()->json([
                        'status' => false,
                        'message' =>
                            'Too many failed attempts. Device blocked.'
                    ], 403);
                }

                return response()->json([
                    'status' => false,
                    'message' => 'Invalid OTP',
                    'remaining_attempts' =>
                        5 - $device->failed_attempts
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | TRUST DEVICE
            |--------------------------------------------------------------------------
            */

            $device->update([

                'trust_level' =>
                    UserDevice::TRUST_TRUSTED,

                'failed_attempts' => 0,

                'last_active_at' => now(),

                'last_ip_address' =>
                    $request->ip(),

                'user_agent' =>
                    $request->userAgent()
            ]);

            /*
            |--------------------------------------------------------------------------
            | CLEAR OTP
            |--------------------------------------------------------------------------
            */

            Cache::forget($cacheKey);

            /*
            |--------------------------------------------------------------------------
            | SECURITY LOG
            |--------------------------------------------------------------------------
            */

            SecurityLogger::log(
                $request,
                'AUTH',
                'device_trusted',
                1,
                200,
                [
                    'user_id' => $user->id,
                    'device_id' => $device->id
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | SYSTEM LOG
            |--------------------------------------------------------------------------
            */

            Log::info('DEVICE TRUSTED', [
                'user_id' => $user->id,
                'device_id' => $device->id
            ]);

            /*
            |--------------------------------------------------------------------------
            | RESPONSE
            |--------------------------------------------------------------------------
            */

            return response()->json([
                'status' => true,
                'message' =>
                    'Device verified successfully',

                'device' => [

                    'device_id' =>
                        $device->device_id,

                    'trust_level' =>
                        $device->trust_level
                ]
            ], 200);

        } catch (\Throwable $e) {

            Log::error('VERIFY DEVICE OTP ERROR', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'OTP verification failed'
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DEVICE STATUS
    |--------------------------------------------------------------------------
    */

    public function status(Request $request)
    {
        try {

            $device = $request
                ->attributes
                ->get('current_device');

            if (!$device) {

                return response()->json([
                    'status' => false,
                    'message' => 'Device not found'
                ], 404);
            }

            return response()->json([

                'status' => true,

                'device' => [

                    'device_id' =>
                        $device->device_id,

                    'trust_level' =>
                        $device->trust_level,

                    'failed_attempts' =>
                        $device->failed_attempts,

                    'last_active_at' =>
                        $device->last_active_at,

                    'last_ip_address' =>
                        $device->last_ip_address,
                ]

            ], 200);

        } catch (\Throwable $e) {

            Log::error('DEVICE STATUS ERROR', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' =>
                    'Unable to fetch device status'
            ], 500);
        }
    }
}