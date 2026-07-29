<?php

namespace Tests\Feature\Api;

use App\Domain\Assessment\Models\AssessmentType;
use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentTypeTest extends TestCase
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

    public function test_can_create_assessment_type(): void
    {
        $response = $this->postJson('/api/v1/assessment-types', [
            'type_name' => 'Continuous Assessment Test',
            'category' => 'Formative',
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['type_name' => 'Continuous Assessment Test']);
    }

    public function test_can_list_assessment_types(): void
    {
        AssessmentType::insert([
            ['type_name' => 'Continuous Assessment Test', 'category' => 'Formative'],
            ['type_name' => 'End of Term Exam', 'category' => 'Summative'],
        ]);

        $response = $this->getJson('/api/v1/assessment-types', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_show_assessment_type(): void
    {
        $type = AssessmentType::create([
            'type_name' => 'Practical Assessment', 'category' => 'Formative',
        ]);

        $response = $this->getJson("/api/v1/assessment-types/{$type->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['type_name' => 'Practical Assessment']);
    }

    public function test_can_update_assessment_type(): void
    {
        $type = AssessmentType::create([
            'type_name' => 'CAT 1', 'category' => 'Formative',
        ]);

        $response = $this->putJson("/api/v1/assessment-types/{$type->id}", [
            'type_name' => 'CAT 2',
            'category' => 'Formative',
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['type_name' => 'CAT 2']);
    }

    public function test_can_delete_assessment_type(): void
    {
        $type = AssessmentType::create([
            'type_name' => 'Quiz', 'category' => 'Formative',
        ]);

        $response = $this->deleteJson("/api/v1/assessment-types/{$type->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('assessment_types', ['id' => $type->id]);
    }

    public function test_create_requires_valid_category(): void
    {
        $response = $this->postJson('/api/v1/assessment-types', [
            'type_name' => 'Invalid CAT',
            'category' => 'Invalid',
        ], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category']);
    }
}
