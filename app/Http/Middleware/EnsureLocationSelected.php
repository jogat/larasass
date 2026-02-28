<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

class EnsureLocationSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Allow location selection/switch endpoints without requiring an active location first.
        if ($request->routeIs('locations.select', 'locations.store', 'locations.switch')) {
            return $next($request);
        }

        // If no authenticated user, let auth middleware handle it
        if (! $user) {
            return $next($request);
        }

        /** @var Collection $locations */
        $user->loadMissing('locations.tenant');
        $locations = $user->locations;


        // User has no locations assigned
        if ($locations->isEmpty()) {
            abort(403, 'No locations assigned to this user.');
        }

        $activeLocationId = session('active_location_id');


//        $activeLocationId = $request->attributes->get('active_location');
//        $activeLocationId = $request->header('X-Location-ID') ?? $request->attributes->get('active_location');
//dd($activeLocationId);
        // Auto-select if only one
        if ($locations->count() === 1) {
            $activeLocation = $locations->first();
            session(['active_location_id' => $activeLocation->id]);
        } else {
            $activeLocation = $locations->firstWhere('id', $activeLocationId);

            if (!$activeLocation) {
                return redirect()->route('locations.select');
            }
        }

        // 🔥 Strict Mode Injection
        $request->attributes->set('active_location', $activeLocation);

        return $next($request);
    }
}
