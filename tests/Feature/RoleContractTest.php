<?php

namespace Tests\Feature;

use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleContractTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['role_name' => 'Administrator', 'description' => 'Full access', 'slug' => 'administrator']);
        $user = User::create([
            'role_id' => 1,
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

    private function roleResourceKeys(): array
    {
        return ['id', 'role_name', 'slug', 'description', 'created_at', 'updated_at'];
    }

    public function test_list_roles_structure(): void
    {
        Role::create(['role_name' => 'Teacher', 'slug' => 'teacher', 'description' => 'Teaching staff']);
        Role::create(['role_name' => 'Student', 'slug' => 'student', 'description' => 'Learner']);

        $response = $this->getJson('/api/v1/roles', $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['*' => $this->roleResourceKeys()]]);

        $json = $response->json();
        $this->assertCount(3, $json['data']);

        foreach ($json['data'] as $role) {
            $this->assertIsInt($role['id']);
            $this->assertIsString($role['role_name']);
            $this->assertIsString($role['slug']);
            $this->assertIsString($role['created_at']);
            $this->assertIsString($role['updated_at']);
        }
    }

    public function test_create_role_auto_generates_slug(): void
    {
        $response = $this->postJson('/api/v1/roles', [
            'role_name' => 'Head Teacher',
            'description' => 'Leads the teaching staff',
        ], $this->authHeaders());

        $response->assertStatus(201);
        $response->assertJsonStructure($this->roleResourceKeys());

        $json = $response->json();
        $this->assertEquals('Head Teacher', $json['role_name']);
        $this->assertEquals('head-teacher', $json['slug']);
        $this->assertDatabaseHas('roles', ['slug' => 'head-teacher']);
    }

    public function test_show_role(): void
    {
        $role = Role::create(['role_name' => 'Teacher', 'slug' => 'teacher', 'description' => 'Teaching staff']);

        $response = $this->getJson("/api/v1/roles/{$role->id}", $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonStructure($this->roleResourceKeys());

        $json = $response->json();
        $this->assertEquals($role->id, $json['id']);
        $this->assertEquals('Teacher', $json['role_name']);
    }

    public function test_update_role(): void
    {
        $role = Role::create(['role_name' => 'Temp', 'slug' => 'temp', 'description' => 'Temporary']);

        $response = $this->putJson("/api/v1/roles/{$role->id}", [
            'role_name' => 'Senior Teacher',
            'description' => 'Senior teaching staff',
        ], $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonStructure($this->roleResourceKeys());

        $json = $response->json();
        $this->assertEquals('Senior Teacher', $json['role_name']);
        $this->assertEquals('senior-teacher', $json['slug']);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'role_name' => 'Senior Teacher',
            'slug' => 'senior-teacher',
        ]);
    }

    public function test_delete_role(): void
    {
        $role = Role::create(['role_name' => 'TempRole', 'slug' => 'temporale', 'description' => 'Will be deleted']);

        $response = $this->deleteJson("/api/v1/roles/{$role->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_validation_errors_on_create(): void
    {
        $this->postJson('/api/v1/roles', [], $this->authHeaders())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['role_name']);
    }

    public function test_duplicate_role_name_validation(): void
    {
        Role::create(['role_name' => 'Teacher', 'slug' => 'teacher', 'description' => '']);

        $this->postJson('/api/v1/roles', ['role_name' => 'Teacher'], $this->authHeaders())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['role_name']);
    }

    public function test_auth_required_for_all_endpoints(): void
    {
        $this->getJson('/api/v1/roles')->assertStatus(401);
        $this->getJson('/api/v1/roles/1')->assertStatus(401);
        $this->postJson('/api/v1/roles', ['role_name' => 'Test'])->assertStatus(401);
        $this->putJson('/api/v1/roles/1', ['role_name' => 'Test'])->assertStatus(401);
        $this->deleteJson('/api/v1/roles/1')->assertStatus(401);
    }
}
