<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        /** @var EloquentCollection<int, Location> $locations */
        $locations = $user->locations()->with('tenant:id,name')->get();

        $activeId = session('active_location_id');
        //        $activeId = $request->attributes->get('active_location');

        /** @var Location|null $active */
        $active = $locations->firstWhere('id', $activeId);

        return Inertia::render('dashboard', [
            'locations' => $locations->map(fn ($loc) => [
                'id' => $loc->id,
                'name' => $loc->name,
                'tenantName' => $loc->tenant->name,
                'role' => $loc->pivot->role,
            ]),
            'activeLocation' => $active ? [
                'id' => $active->id,
                'name' => $active->name,
                'tenantName' => $active->tenant->name,
                'role' => $active->pivot->role,
            ] : null,
        ]);
    }
}
