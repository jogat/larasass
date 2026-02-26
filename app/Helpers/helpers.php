<?php


use App\Models\Location;

if (!function_exists('active_location')) {
    function active_location(): ?Location
    {
        return auth()->user()
            ? auth()->user()
                ->locations()
                ->find(session('active_location_id'))
            : null;
    }

}

if (!function_exists('current_location')) {
    function current_location(Request $request)
    {
        return $request->attributes->get('active_location');
    }
}

