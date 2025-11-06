<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow admin users to bypass maintenance mode
        $user = $request->user();
        if ($user && (bool) ($user->is_admin ?? false)) {
            return $next($request);
        }

        // Check maintenance mode setting
        try {
            $maintenanceMode = Setting::where('key', 'maintenance_mode')
                ->where('type', 'boolean')
                ->first();

            $isMaintenanceEnabled = $maintenanceMode && (bool) $maintenanceMode->value;
        } catch (\Exception $e) {
            // If database is not available, don't enable maintenance mode
            $isMaintenanceEnabled = false;
        }

        // Allow access to admin routes even during maintenance
        if ($isMaintenanceEnabled && !$request->is('admin*') && !$request->is('login') && !$request->is('register')) {
            return response()->view('pages.maintenance', [], 503);
        }

        return $next($request);
    }
}

