<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LocationContextController extends Controller
{
    public function test()
    {
        $activeLocation = [
            'id'=> '1',
            'name'=> 'test',
            'tenantName'=> 'tenant name',
            'role'=> 'role1',
        ];

        $location = [
            'id'=> '2',
            'name'=> 'test 2',
            'tenantName'=> 'tenant name 2',
            'role'=> 'role2 2',
        ];

        return Inertia::render('dashboard', [
//            'locations' => Inertia::defer(fn () => [$activeLocation]),
            'locations' => [$activeLocation, $location],
            'activeLocation' => $activeLocation,
        ]);
    }

    public function select(Request $request)
    {
        $user = $request->user();

        $locations = $user->locations()
            ->with('tenant:id,name,slug') // show tenant name in UI
            ->get()
            ->map(fn (Location $loc) => [
                'id' => $loc->id,
                'name' => $loc->name,
                'tenant' => [
                    'name' => $loc->tenant->name,
                    'slug' => $loc->tenant->slug,
                ],
                'role' => $loc->pivot->role,
            ]);

        return Inertia::render('locations/select', [
            'locations' => $locations,
            'activeLocationId' => session('active_location_id'),
        ]);
    }

    public function store(Request $request)
    {
        return $this->setActiveLocation($request);
    }

    public function switch(Request $request)
    {
        return $this->setActiveLocation($request);
    }

    private function setActiveLocation(Request $request)
    {
        $request->validate([
            'location_id' => ['required', 'integer'],
        ]);

        $user = $request->user();

        // Critical: verify membership (never trust ID)
        $location = $user->locations()
            ->where('locations.id', $request->integer('location_id'))
            ->firstOrFail();

        session(['active_location_id' => $location->id]);

        // Optional: after switch, you can redirect back or to dashboard
        return redirect()->route('dashboard');
    }

    //    public function index(Request $request)
    //    {
    //        $location = $request->attributes->get('active_location');
    //
    //        // Safe to assume:
    //        // - User belongs to this location
    //        // - It exists
    //        // - It has tenant loaded
    //
    //        return Inertia::render('dashboard', [
    //            'location' => [
    //                'id' => $location->id,
    //                'name' => $location->name,
    //                'tenant' => $location->tenant->name,
    //                'role' => $location->pivot->role,
    //            ]
    //        ]);
    //    }
}
