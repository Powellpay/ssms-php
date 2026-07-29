<?php

namespace Tests\Feature;

use App\Domain\Academic\Models\ClassLevel;
use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassLevelContractTest extends TestCase
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

    private function resourceKeys(): array
    {
        return ['id', 'level_name', 'numeric_level', 'description', 'created_at', 'updated_at'];
    }

    public function test_list_class_levels_structure(): void
    {
        ClassLevel::create(['level_name' => 'S1', 'numeric_level' => 1]);
        ClassLevel::create(['level_name' => 'S2', 'numeric_level' => 2]);

        $response = $this->getJson('/api/v1/class-levels', $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['*' => $this->resourceKeys()]]);

        $json = $response->json();
        $this->assertCount(2, $json['data']);

        foreach ($json['data'] as $level) {
            $this->assertIsInt($level['id']);
            $this->assertIsString($level['level_name']);
            $this->assertIsInt($level['numeric_level']);
        }
    }

    public function test_create_class_level(): void
    {
        $response = $this->postJson('/api/v1/class-levels', [
            'level_name' => 'S3',
            'numeric_level' => 3,
            'description' => 'Senior Three',
        ], $this->authHeaders());

        $response->assertStatus(201);
        $response->assertJsonStructure($this->resourceKeys());
        $this->assertEquals('S3', $response->json('level_name'));
        $this->assertEquals(3, $response->json('numeric_level'));
        $this->assertDatabaseHas('class_levels', ['level_name' => 'S3']);
    }

    public function test_create_class_level_validation(): void
    {
        $this->postJson('/api/v1/class-levels', [], $this->authHeaders())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['level_name', 'numeric_level']);
    }

    public function test_create_class_level_unique_name(): void
    {
        ClassLevel::create(['level_name' => 'S1', 'numeric_level' => 1]);

        $this->postJson('/api/v1/class-levels', [
            'level_name' => 'S1', 'numeric_level' => 1,
        ], $this->authHeaders())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['level_name']);
    }

    public function test_show_class_level(): void
    {
        $level = ClassLevel::create(['level_name' => 'S4', 'numeric_level' => 4]);

        $response = $this->getJson("/api/v1/class-levels/{$level->id}", $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonStructure($this->resourceKeys());
        $this->assertEquals('S4', $response->json('level_name'));
    }

    public function test_update_class_level(): void
    {
        $level = ClassLevel::create(['level_name' => 'S5', 'numeric_level' => 5]);

        $response = $this->putJson("/api/v1/class-levels/{$level->id}", [
            'level_name' => 'Senior Five',
            'numeric_level' => 5,
        ], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertEquals('Senior Five', $response->json('level_name'));
        $this->assertDatabaseHas('class_levels', ['id' => $level->id, 'level_name' => 'Senior Five']);
    }

    public function test_delete_class_level(): void
    {
        $level = ClassLevel::create(['level_name' => 'S6', 'numeric_level' => 6]);

        $response = $this->deleteJson("/api/v1/class-levels/{$level->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
        $this->assertDatabaseMissing('class_levels', ['id' => $level->id]);
    }

    public function test_auth_required_for_class_level_endpoints(): void
    {
        $this->getJson('/api/v1/class-levels')->assertStatus(401);
        $this->getJson('/api/v1/class-levels/1')->assertStatus(401);
        $this->postJson('/api/v1/class-levels', [])->assertStatus(401);
        $this->putJson('/api/v1/class-levels/1', [])->assertStatus(401);
        $this->deleteJson('/api/v1/class-levels/1')->assertStatus(401);
    }
}
