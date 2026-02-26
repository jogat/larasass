<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoTenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use the first user (or create one)
        $user = User::first() ?? User::factory()->create([
            'name' => 'Zamy',
            'email' => 'zamy@example.com',
        ]);

        $tenant = Tenant::firstOrCreate(
            ['slug' => 'demo-tenant'],
            ['name' => 'Demo Tenant']
        );

        $loc1 = Location::firstOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'hq'],
            ['name' => 'HQ']
        );

        $loc2 = Location::firstOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'north'],
            ['name' => 'North Location']
        );

        // Attach user with different roles
        $user->locations()->syncWithoutDetaching([
            $loc1->id => ['role' => 'admin'],
            $loc2->id => ['role' => 'viewer'],
        ]);
    }
}
