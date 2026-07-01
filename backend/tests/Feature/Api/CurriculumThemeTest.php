<?php

namespace Tests\Feature\Api;

use App\Models\ClassLevel;
use App\Models\CurriculumTheme;
use App\Models\Role;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurriculumThemeTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private Subject $subject;
    private ClassLevel $classLevel;

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

        $this->subject = Subject::create([
            'subject_code' => 'MTC',
            'subject_name' => 'Mathematics',
            'category' => 'Core',
        ]);

        $this->classLevel = ClassLevel::create(['level_name' => 'S1', 'numeric_level' => 1]);
    }

    private function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_can_create_theme(): void
    {
        $response = $this->postJson('/api/curriculum-themes', [
            'subject_id' => $this->subject->id,
            'class_level_id' => $this->classLevel->id,
            'theme_code' => 'MTC-S1-01',
            'theme_name' => 'Numbers and Operations',
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['theme_code' => 'MTC-S1-01']);
    }

    public function test_can_list_themes(): void
    {
        CurriculumTheme::create([
            'subject_id' => $this->subject->id,
            'class_level_id' => $this->classLevel->id,
            'theme_code' => 'MTC-S1-01',
            'theme_name' => 'Numbers and Operations',
        ]);

        $response = $this->getJson('/api/curriculum-themes', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_update_theme(): void
    {
        $theme = CurriculumTheme::create([
            'subject_id' => $this->subject->id,
            'class_level_id' => $this->classLevel->id,
            'theme_code' => 'MTC-S1-01',
            'theme_name' => 'Numbers',
        ]);

        $response = $this->putJson("/api/curriculum-themes/{$theme->id}", [
            'subject_id' => $this->subject->id,
            'class_level_id' => $this->classLevel->id,
            'theme_code' => 'MTC-S1-01',
            'theme_name' => 'Numbers and Operations',
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['theme_name' => 'Numbers and Operations']);
    }
}
