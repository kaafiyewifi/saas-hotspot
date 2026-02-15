<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_access_only_attached_active_tenant_context(): void
    {
        $tenantA = Tenant::create([
            'name' => 'Tenant A',
            'slug' => 'tenant-a',
            'timezone' => 'UTC',
            'currency' => 'USD',
            'status' => 'active',
        ]);

        $tenantB = Tenant::create([
            'name' => 'Tenant B',
            'slug' => 'tenant-b',
            'timezone' => 'UTC',
            'currency' => 'USD',
            'status' => 'active',
        ]);

        $user = User::factory()->create([
            'is_super_admin' => false,
        ]);

        $tenantA->users()->attach($user->id, [
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get('/t/tenant-a/context')
            ->assertOk();

        $this->actingAs($user)
            ->get('/t/tenant-b/context')
            ->assertNotFound();
    }
}
