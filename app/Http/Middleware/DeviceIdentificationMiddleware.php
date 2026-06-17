<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UserDevice;
use App\Services\SecurityLogger;

class DeviceIdentificationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {

            SecurityLogger::log(
                $request,
                'SECURITY',
                'unauthenticated_request',
                8,
                401
            );

            return response()->json([
                'error' => 'Unauthenticated'
            ], 401);
        }

        // Device ID
        $deviceId =
            $request->header('X-Device-ID') ??
            $request->header('x-device-id');

// Device ID
$deviceId =
    $request->header('X-Device-ID') ??
    $request->header('x-device-id');

\Log::info('DEVICE HEADER DEBUG', [
    'device_id' => $deviceId,
    'all_headers' => $request->headers->all(),
    'url' => $request->fullUrl(),
    'method' => $request->method(),
]);


        if (!$deviceId) {

            SecurityLogger::log(
                $request,
                'SECURITY',
                'missing_device_id',
                7,
                400
            );

            return response()->json([
                'error' => 'Device ID missing'
            ], 400);
        }

        // ONLY FIND DEVICE
        $device = UserDevice::where(
                'user_id',
                $user->id
            )
            ->where(
                'device_id',
                $deviceId
            )
            ->first();

        // BLOCK UNKNOWN DEVICE
        if (!$device) {

            SecurityLogger::log(
                $request,
                'SECURITY',
                'unknown_device',
                9,
                403,
                [
                    'device_id' => $deviceId
                ]
            );

            return response()->json([
                'error' => 'Unknown device'
            ], 403);
        }

        // UPDATE ACTIVITY
        $device->update([
            'last_ip_address' => $request->ip(),
            'last_active_at' => now(),
            'user_agent' => $request->userAgent()
        ]);

        // STORE CURRENT DEVICE
        $request->attributes->set(
            'current_device',
            $device
        );

        SecurityLogger::log(
            $request,
            'SECURITY',
            'device_verified',
            2,
            200,
            [
                'device_id' => $device->device_id,
                'trust_level' => $device->trust_level
            ]
        );

        return $next($request);
    }
}