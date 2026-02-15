<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TenantAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_super_admin_cannot_create_tenant(): void
    {
        $user = User::factory()->create([
            'is_super_admin' => false,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/admin/tenants', [
            'name' => 'Tenant One',
            'slug' => 'tenant-one',
            'timezone' => 'UTC',
            'currency' => 'USD',
            'status' => 'active',
        ]);

        $response->assertForbidden();
    }

    public function test_super_admin_can_create_tenant(): void
    {
        $user = User::factory()->create([
            'is_super_admin' => true,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/admin/tenants', [
            'name' => 'Tenant Two',
            'slug' => 'tenant-two',
            'timezone' => 'UTC',
            'currency' => 'USD',
            'status' => 'active',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('tenants', [
            'slug' => 'tenant-two',
        ]);
    }
}
