<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationContextTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_multiple_locations_must_select_location_before_dashboard(): void
    {
        // Arrange: user with 2 locations
        $user = User::factory()->create();

        $tenant = Tenant::create([
            'name' => 'Demo Tenant',
            'slug' => 'demo-tenant',
        ]);

        $loc1 = Location::create([
            'tenant_id' => $tenant->id,
            'name' => 'HQ',
            'slug' => 'hq',
        ]);

        $loc2 = Location::create([
            'tenant_id' => $tenant->id,
            'name' => 'North',
            'slug' => 'north',
        ]);

        $user->locations()->attach($loc1->id, ['role' => 'admin']);
        $user->locations()->attach($loc2->id, ['role' => 'viewer']);

        // Act: visit dashboard without active_location_id in session
        $response = $this->actingAs($user)->get(route('dashboard'));

        // Assert: redirected to location selection
        $response->assertRedirect(route('locations.select'));
    }

    public function test_user_with_one_location_is_auto_selected_and_can_view_dashboard(): void
    {
        $user = User::factory()->create();

        $tenant = Tenant::create([
            'name' => 'Demo Tenant',
            'slug' => 'demo-tenant',
        ]);

        $loc = Location::create([
            'tenant_id' => $tenant->id,
            'name' => 'HQ',
            'slug' => 'hq',
        ]);

        $user->locations()->attach($loc->id, ['role' => 'admin']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSessionHas('active_location_id', $loc->id);
    }
}
