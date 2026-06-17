<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireTrustedDevice
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $device = $request->attributes
            ->get('current_device');

        if (!$device) {

            return response()->json([
                'status' => false,
                'message' => 'Device not detected'
            ], 403);
        }

        if ($device->trust_level !== 'TRUSTED') {

            return response()->json([
                'status' => false,
                'message' =>
                    'Untrusted device. Verify device first.',
                'trust_level' =>
                    $device->trust_level
            ], 403);
        }

        return $next($request);
    }
}