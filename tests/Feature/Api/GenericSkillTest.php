<?php

namespace Tests\Feature\Api;

use App\Domain\Curriculum\Models\GenericSkill;
use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenericSkillTest extends TestCase
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

    public function test_can_create_generic_skill(): void
    {
        $response = $this->postJson('/api/v1/generic-skills', [
            'skill_name' => 'Critical Thinking',
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['skill_name' => 'Critical Thinking']);
    }

    public function test_can_list_generic_skills(): void
    {
        GenericSkill::create(['skill_name' => 'Critical Thinking']);
        GenericSkill::create(['skill_name' => 'Problem Solving']);

        $response = $this->getJson('/api/v1/generic-skills', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_create_requires_unique_name(): void
    {
        GenericSkill::create(['skill_name' => 'Critical Thinking']);

        $response = $this->postJson('/api/v1/generic-skills', [
            'skill_name' => 'Critical Thinking',
        ], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['skill_name']);
    }
}
