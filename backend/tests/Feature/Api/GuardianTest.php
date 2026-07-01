<?php

namespace Tests\Feature\Api;

use App\Domain\Students\Models\Guardian;
use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuardianTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $role = Role::create(['role_name' => 'Administrator']);
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

    public function test_can_list_guardians(): void
    {
        Guardian::create(['first_name' => 'Peter', 'last_name' => 'Lutalo', 'relationship' => 'Father', 'phone' => '0771000001']);
        Guardian::create(['first_name' => 'Mary', 'last_name' => 'Nakato', 'relationship' => 'Mother', 'phone' => '0771000002']);

        $response = $this->getJson('/api/guardians', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_create_guardian(): void
    {
        $response = $this->postJson('/api/guardians', [
            'first_name' => 'John',
            'last_name' => 'Mukasa',
            'relationship' => 'Uncle',
            'phone' => '0771000003',
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['first_name' => 'John']);
    }

    public function test_can_show_guardian(): void
    {
        $guardian = Guardian::create(['first_name' => 'Sarah', 'last_name' => 'Nabatanzi', 'relationship' => 'Mother', 'phone' => '0771000004']);

        $response = $this->getJson("/api/guardians/{$guardian->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['first_name' => 'Sarah']);
    }

    public function test_can_update_guardian(): void
    {
        $guardian = Guardian::create(['first_name' => 'Tom', 'last_name' => 'Kato', 'relationship' => 'Father', 'phone' => '0771000005']);

        $response = $this->putJson("/api/guardians/{$guardian->id}", [
            'first_name' => 'Tom',
            'last_name' => 'Kato',
            'relationship' => 'Father',
            'phone' => '0771000006',
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['phone' => '0771000006']);
    }

    public function test_can_delete_guardian(): void
    {
        $guardian = Guardian::create(['first_name' => 'Alice', 'last_name' => 'Nambi', 'relationship' => 'Mother', 'phone' => '0771000007']);

        $response = $this->deleteJson("/api/guardians/{$guardian->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('guardians', ['id' => $guardian->id]);
    }
}
