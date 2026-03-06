<?php

use App\Models\Location;
use Illuminate\Http\Request;

if (! function_exists('active_location')) {
    function active_location(): ?Location
    {
        $user = auth()->user();

        if (! $user) {
            return null;
        }

        $activeLocationId = session('active_location_id');
        if (! is_numeric($activeLocationId)) {
            return null;
        }

        /** @var ?Location $location */
        $location = $user->locations()
            ->whereKey((int) $activeLocationId)
            ->first();

        return $location;
    }

}

if (! function_exists('current_location')) {
    function current_location(Request $request): ?Location
    {
        $activeLocation = $request->attributes->get('active_location');

        return $activeLocation instanceof Location ? $activeLocation : null;
    }
}
