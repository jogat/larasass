<?php

namespace Tests\Browser;

use App\Models\Location;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LocationFlowTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_user_can_login_select_location_switch_and_logout(): void
    {

        $password = 'secret123';
        // Arrange
        $user = User::factory()->create([
            'password' => $password
        ]);

        $tenant = Tenant::create(['name' => 'Demo Tenant', 'slug' => 'demo-tenant']);

        $hq = Location::create(['tenant_id' => $tenant->id, 'name' => 'HQ', 'slug' => 'hq']);
        $north = Location::create(['tenant_id' => $tenant->id, 'name' => 'North', 'slug' => 'north']);

        $user->locations()->attach($hq->id, ['role' => 'admin']);
        $user->locations()->attach($north->id, ['role' => 'viewer']);

        $this->browse(function (Browser $browser) use ($user, $hq, $north, $password) {

            // Login
            $browser->visit('/login');

            $browser->waitForLocation('/login')
                ->type('#email', $user->email)
                ->type('#password', $password)
                ->press('Log in');

            // Inertia page change = wait for something stable
            $browser->waitForLocation('/locations/select');

            // Select HQ (make sure your <select> has a stable selector)
            $browser->select('location_id', (string) $hq->id)
                ->press('Continue');

            $browser->waitForLocation('/dashboard')
                ->assertSee('HQ');

            // Switch to North (again, use stable selectors)
            $browser->select('location_id', (string) $north->id)
                ->press('Switch');

            $browser->waitForLocation('/dashboard')
                ->assertSee('North');

            // Logout
            $browser->press('Logout')
                ->waitForLocation('/');
        });
    }
}
