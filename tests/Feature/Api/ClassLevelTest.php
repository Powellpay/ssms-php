<?php

namespace Tests\Feature\Api;

use App\Domain\Academic\Models\ClassLevel;
use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassLevelTest extends TestCase
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

    public function test_can_list_class_levels(): void
    {
        ClassLevel::create(['level_name' => 'S1', 'numeric_level' => 1]);
        ClassLevel::create(['level_name' => 'S2', 'numeric_level' => 2]);

        $response = $this->getJson('/api/v1/class-levels', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_create_class_level(): void
    {
        $response = $this->postJson('/api/v1/class-levels', [
            'level_name' => 'S3',
            'numeric_level' => 3,
            'description' => 'Senior Three',
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['level_name' => 'S3']);
    }

    public function test_can_show_class_level(): void
    {
        $classLevel = ClassLevel::create(['level_name' => 'S4', 'numeric_level' => 4]);

        $response = $this->getJson("/api/v1/class-levels/{$classLevel->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['level_name' => 'S4']);
    }

    public function test_can_update_class_level(): void
    {
        $classLevel = ClassLevel::create(['level_name' => 'S5', 'numeric_level' => 5]);

        $response = $this->putJson("/api/v1/class-levels/{$classLevel->id}", [
            'level_name' => 'Senior Five',
            'numeric_level' => 5,
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['level_name' => 'Senior Five']);
    }

    public function test_can_delete_class_level(): void
    {
        $classLevel = ClassLevel::create(['level_name' => 'S6', 'numeric_level' => 6]);

        $response = $this->deleteJson("/api/v1/class-levels/{$classLevel->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('class_levels', ['id' => $classLevel->id]);
    }

    public function test_create_requires_unique_name(): void
    {
        ClassLevel::create(['level_name' => 'S1', 'numeric_level' => 1]);

        $response = $this->postJson('/api/v1/class-levels', [
            'level_name' => 'S1',
            'numeric_level' => 1,
        ], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['level_name']);
    }
}
