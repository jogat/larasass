<?php

namespace Tests\Feature\Auth;

use App\Models\Location;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_login_page(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_user_can_register_and_access_dashboard(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user = User::query()->where('email', 'test@example.com')->firstOrFail();
        $tenant = Tenant::create(['name' => 'Demo Tenant', 'slug' => 'demo-tenant']);
        $location = Location::create([
            'tenant_id' => $tenant->id,
            'name' => 'HQ',
            'slug' => 'hq',
        ]);
        $user->locations()->attach($location->id, ['role' => 'admin']);

        $this->get('/dashboard')->assertOk();
    }

    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);

        // Some kits expect POST /logout, some accept it; yours likely does.
        $this->post(route('logout'))->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }
}
