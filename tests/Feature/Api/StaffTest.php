<?php

namespace Tests\Feature\Api;

use App\Domain\Auth\Models\Role;
use App\Domain\Staff\Models\Staff;
use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffTest extends TestCase
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

    public function test_can_create_staff(): void
    {
        $response = $this->postJson('/api/v1/staff', [
            'staff_no' => 'STF-001',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'gender' => 'Female',
            'status' => 'active',
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['staff_no' => 'STF-001']);
    }

    public function test_can_list_staff(): void
    {
        Staff::insert([
            ['staff_no' => 'STF-001', 'first_name' => 'Jane', 'last_name' => 'Doe', 'gender' => 'Female', 'status' => 'active'],
            ['staff_no' => 'STF-002', 'first_name' => 'John', 'last_name' => 'Smith', 'gender' => 'Male', 'status' => 'active'],
        ]);

        $response = $this->getJson('/api/v1/staff', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_delete_staff(): void
    {
        $staff = Staff::create([
            'staff_no' => 'STF-003', 'first_name' => 'Delete', 'last_name' => 'Me',
            'gender' => 'Male', 'status' => 'inactive',
        ]);

        $response = $this->deleteJson("/api/v1/staff/{$staff->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('staff', ['id' => $staff->id]);
    }
}
