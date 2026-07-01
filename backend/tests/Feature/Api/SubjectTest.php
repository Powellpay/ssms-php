<?php

namespace Tests\Feature\Api;

use App\Domain\Auth\Models\Role;
use App\Domain\Curriculum\Models\Subject;
use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectTest extends TestCase
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

    public function test_can_list_subjects(): void
    {
        Subject::create(['subject_code' => 'MTC', 'subject_name' => 'Mathematics', 'category' => 'Core']);
        Subject::create(['subject_code' => 'ENG', 'subject_name' => 'English', 'category' => 'Core']);

        $response = $this->getJson('/api/subjects', $this->authHeaders());

        $response->assertStatus(200)->assertJsonCount(2, 'data');
    }

    public function test_can_create_subject(): void
    {
        $response = $this->postJson('/api/subjects', [
            'subject_code' => 'PHY',
            'subject_name' => 'Physics',
            'category' => 'Core',
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['subject_code' => 'PHY']);
    }

    public function test_can_show_subject(): void
    {
        $subject = Subject::create(['subject_code' => 'BIO', 'subject_name' => 'Biology', 'category' => 'Core']);

        $response = $this->getJson("/api/subjects/{$subject->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['subject_name' => 'Biology']);
    }

    public function test_can_update_subject(): void
    {
        $subject = Subject::create(['subject_code' => 'CHE', 'subject_name' => 'Chemistry', 'category' => 'Core']);

        $response = $this->putJson("/api/subjects/{$subject->id}", [
            'subject_code' => 'CHE',
            'subject_name' => 'Advanced Chemistry',
            'category' => 'Core',
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['subject_name' => 'Advanced Chemistry']);
    }

    public function test_can_delete_subject(): void
    {
        $subject = Subject::create(['subject_code' => 'KIS', 'subject_name' => 'Kiswahili', 'category' => 'Core']);

        $response = $this->deleteJson("/api/subjects/{$subject->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
    }

    public function test_create_requires_unique_code(): void
    {
        Subject::create(['subject_code' => 'MTC', 'subject_name' => 'Maths', 'category' => 'Core']);

        $response = $this->postJson('/api/subjects', [
            'subject_code' => 'MTC',
            'subject_name' => 'Mathematics',
            'category' => 'Core',
        ], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['subject_code']);
    }

    public function test_requires_valid_category(): void
    {
        $response = $this->postJson('/api/subjects', [
            'subject_code' => 'XXX',
            'subject_name' => 'Unknown',
            'category' => 'Invalid',
        ], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category']);
    }
}
