<?php

namespace Tests\Feature\Api;

use App\Models\ClassLevel;
use App\Models\CurriculumTheme;
use App\Models\LearningOutcome;
use App\Models\Role;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningOutcomeTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private CurriculumTheme $theme;

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

        $subject = Subject::create([
            'subject_code' => 'MTC',
            'subject_name' => 'Mathematics',
            'category' => 'Core',
        ]);

        $classLevel = ClassLevel::create(['level_name' => 'S1', 'numeric_level' => 1]);

        $this->theme = CurriculumTheme::create([
            'subject_id' => $subject->id,
            'class_level_id' => $classLevel->id,
            'theme_code' => 'MTC-S1-01',
            'theme_name' => 'Numbers and Operations',
        ]);
    }

    private function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_can_create_learning_outcome(): void
    {
        $response = $this->postJson('/api/learning-outcomes', [
            'theme_id' => $this->theme->id,
            'outcome_code' => 'MTC-S1-01-01',
            'description' => 'Count and write numbers up to 100',
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['outcome_code' => 'MTC-S1-01-01']);
    }

    public function test_can_list_learning_outcomes(): void
    {
        LearningOutcome::create([
            'theme_id' => $this->theme->id,
            'outcome_code' => 'MTC-S1-01-01',
            'description' => 'Count and write numbers up to 100',
        ]);

        $response = $this->getJson('/api/learning-outcomes', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }
}
