<?php

namespace Tests\Feature\Api;

use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $role = Role::create(['role_name' => 'Administrator', 'description' => 'Full access']);
        $user = User::create([
            'role_id' => $role->id,
            'username' => 'admin',
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $this->token = $user->createToken('test-token')->plainTextToken;
    }

    private function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_can_list_roles(): void
    {
        Role::create(['role_name' => 'Teacher', 'description' => 'Teaching staff']);

        $response = $this->getJson('/api/roles', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_create_role(): void
    {
        $response = $this->postJson('/api/roles', [
            'role_name' => 'Bursar',
            'description' => 'Manages fees',
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['role_name' => 'Bursar']);
    }

    public function test_can_show_role(): void
    {
        $role = Role::create(['role_name' => 'Teacher', 'description' => 'Teaching staff']);

        $response = $this->getJson("/api/roles/{$role->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['role_name' => 'Teacher']);
    }

    public function test_can_update_role(): void
    {
        $role = Role::create(['role_name' => 'Temp', 'description' => 'Temporary']);

        $response = $this->putJson("/api/roles/{$role->id}", [
            'role_name' => 'Permanent',
            'description' => 'Updated',
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['role_name' => 'Permanent']);
    }

    public function test_can_delete_role(): void
    {
        $role = Role::create(['role_name' => 'TempRole', 'description' => 'Will be deleted']);

        $response = $this->deleteJson("/api/roles/{$role->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_create_role_requires_unique_name(): void
    {
        Role::create(['role_name' => 'Teacher', 'description' => '']);

        $response = $this->postJson('/api/roles', [
            'role_name' => 'Teacher',
        ], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role_name']);
    }

    public function test_unauthenticated_request_fails(): void
    {
        $response = $this->getJson('/api/roles');

        $response->assertStatus(401);
    }
}
