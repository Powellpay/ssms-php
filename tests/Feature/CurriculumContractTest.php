<?php

namespace Tests\Feature;

use App\Domain\Academic\Models\AcademicYear;
use App\Domain\Academic\Models\ClassLevel;
use App\Domain\Academic\Models\Stream;
use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Models\User;
use App\Domain\Curriculum\Models\ClassSubject;
use App\Domain\Curriculum\Models\CurriculumTheme;
use App\Domain\Curriculum\Models\GenericSkill;
use App\Domain\Curriculum\Models\LearningOutcome;
use App\Domain\Curriculum\Models\Subject;
use App\Domain\Curriculum\Models\SubjectTeacher;
use App\Domain\Staff\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurriculumContractTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private Subject $subject;
    private ClassLevel $classLevel;
    private AcademicYear $academicYear;
    private Stream $stream;
    private Staff $staff;
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

        $this->subject = Subject::create(['subject_code' => 'MTC', 'subject_name' => 'Mathematics', 'category' => 'Core']);
        $this->classLevel = ClassLevel::create(['level_name' => 'S1', 'numeric_level' => 1]);
        $this->academicYear = AcademicYear::create([
            'year_name' => '2026', 'start_date' => '2026-02-03', 'end_date' => '2026-12-04', 'is_current' => true,
        ]);
        $this->stream = Stream::create([
            'class_level_id' => $this->classLevel->id,
            'academic_year_id' => $this->academicYear->id,
            'stream_name' => 'S1 East',
        ]);
        $this->staff = Staff::create([
            'staff_no' => 'STF-001', 'first_name' => 'Jane', 'last_name' => 'Doe', 'gender' => 'Female', 'status' => 'active',
        ]);
        $this->theme = CurriculumTheme::create([
            'subject_id' => $this->subject->id, 'class_level_id' => $this->classLevel->id,
            'theme_code' => 'MTC-S1-01', 'theme_name' => 'Numbers and Operations',
        ]);
    }

    private function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_list_subjects_structure(): void
    {
        $response = $this->getJson('/api/v1/subjects', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['*' => ['id', 'subject_code', 'subject_name', 'category', 'description', 'created_at', 'updated_at']]]);
    }

    public function test_create_subject(): void
    {
        $response = $this->postJson('/api/v1/subjects', [
            'subject_code' => 'PHY', 'subject_name' => 'Physics', 'category' => 'Core',
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['subject_code' => 'PHY']);
    }

    public function test_update_subject(): void
    {
        $response = $this->putJson("/api/v1/subjects/{$this->subject->id}", [
            'subject_code' => 'MTC', 'subject_name' => 'Advanced Mathematics', 'category' => 'Core',
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['subject_name' => 'Advanced Mathematics']);
    }

    public function test_delete_subject(): void
    {
        $response = $this->deleteJson("/api/v1/subjects/{$this->subject->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('subjects', ['id' => $this->subject->id]);
    }

    public function test_subject_validation(): void
    {
        $response = $this->postJson('/api/v1/subjects', [], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['subject_code', 'subject_name', 'category']);
    }

    public function test_list_class_subjects_structure(): void
    {
        $response = $this->getJson('/api/v1/class-subjects', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['*' => ['id', 'class_level_id', 'subject_id', 'is_compulsory', 'created_at', 'updated_at']]]);
    }

    public function test_create_class_subject(): void
    {
        $response = $this->postJson('/api/v1/class-subjects', [
            'class_level_id' => $this->classLevel->id, 'subject_id' => $this->subject->id, 'is_compulsory' => true,
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['class_level_id' => $this->classLevel->id]);
    }

    public function test_update_class_subject(): void
    {
        $cs = ClassSubject::create(['class_level_id' => $this->classLevel->id, 'subject_id' => $this->subject->id]);

        $response = $this->putJson("/api/v1/class-subjects/{$cs->id}", [
            'class_level_id' => $this->classLevel->id, 'subject_id' => $this->subject->id, 'is_compulsory' => true,
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['is_compulsory' => true]);
    }

    public function test_delete_class_subject(): void
    {
        $cs = ClassSubject::create(['class_level_id' => $this->classLevel->id, 'subject_id' => $this->subject->id]);

        $response = $this->deleteJson("/api/v1/class-subjects/{$cs->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('class_subjects', ['id' => $cs->id]);
    }

    public function test_class_subject_validation(): void
    {
        $response = $this->postJson('/api/v1/class-subjects', [], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['class_level_id', 'subject_id']);
    }

    public function test_list_subject_teachers_structure(): void
    {
        $response = $this->getJson('/api/v1/subject-teachers', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['*' => ['id', 'subject_id', 'stream_id', 'staff_id', 'academic_year_id', 'created_at', 'updated_at']]]);
    }

    public function test_create_subject_teacher(): void
    {
        $response = $this->postJson('/api/v1/subject-teachers', [
            'subject_id' => $this->subject->id, 'stream_id' => $this->stream->id,
            'staff_id' => $this->staff->id, 'academic_year_id' => $this->academicYear->id,
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['subject_id' => $this->subject->id]);
    }

    public function test_delete_subject_teacher(): void
    {
        $st = SubjectTeacher::create([
            'subject_id' => $this->subject->id, 'stream_id' => $this->stream->id,
            'staff_id' => $this->staff->id, 'academic_year_id' => $this->academicYear->id,
        ]);

        $response = $this->deleteJson("/api/v1/subject-teachers/{$st->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('subject_teachers', ['id' => $st->id]);
    }

    public function test_subject_teacher_validation(): void
    {
        $response = $this->postJson('/api/v1/subject-teachers', [], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['subject_id', 'stream_id', 'staff_id', 'academic_year_id']);
    }

    public function test_list_curriculum_themes_structure(): void
    {
        $response = $this->getJson('/api/v1/curriculum-themes', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['*' => ['id', 'subject_id', 'class_level_id', 'theme_code', 'theme_name', 'description', 'created_at', 'updated_at']]]);
    }

    public function test_create_curriculum_theme(): void
    {
        $response = $this->postJson('/api/v1/curriculum-themes', [
            'subject_id' => $this->subject->id, 'class_level_id' => $this->classLevel->id,
            'theme_code' => 'MTC-S1-02', 'theme_name' => 'Algebra',
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['theme_code' => 'MTC-S1-02']);
    }

    public function test_update_curriculum_theme(): void
    {
        $response = $this->putJson("/api/v1/curriculum-themes/{$this->theme->id}", [
            'subject_id' => $this->subject->id, 'class_level_id' => $this->classLevel->id,
            'theme_code' => 'MTC-S1-01', 'theme_name' => 'Numbers and Algebra',
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['theme_name' => 'Numbers and Algebra']);
    }

    public function test_delete_curriculum_theme(): void
    {
        $theme = CurriculumTheme::create([
            'subject_id' => $this->subject->id, 'class_level_id' => $this->classLevel->id,
            'theme_code' => 'DEL-THEME', 'theme_name' => 'To Delete',
        ]);

        $response = $this->deleteJson("/api/v1/curriculum-themes/{$theme->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('curriculum_themes', ['id' => $theme->id]);
    }

    public function test_curriculum_theme_validation(): void
    {
        $response = $this->postJson('/api/v1/curriculum-themes', [], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['subject_id', 'class_level_id', 'theme_name']);
    }

    public function test_list_learning_outcomes_structure(): void
    {
        $response = $this->getJson('/api/v1/learning-outcomes', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['*' => ['id', 'theme_id', 'outcome_code', 'description', 'created_at', 'updated_at']]]);
    }

    public function test_create_learning_outcome(): void
    {
        $response = $this->postJson('/api/v1/learning-outcomes', [
            'theme_id' => $this->theme->id, 'outcome_code' => 'MTC-S1-01-01', 'description' => 'Count numbers up to 100',
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['outcome_code' => 'MTC-S1-01-01']);
    }

    public function test_update_learning_outcome(): void
    {
        $lo = LearningOutcome::create([
            'theme_id' => $this->theme->id, 'outcome_code' => 'MTC-S1-01-01', 'description' => 'Original',
        ]);

        $response = $this->putJson("/api/v1/learning-outcomes/{$lo->id}", [
            'theme_id' => $this->theme->id, 'outcome_code' => 'MTC-S1-01-01', 'description' => 'Updated description',
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['description' => 'Updated description']);
    }

    public function test_delete_learning_outcome(): void
    {
        $lo = LearningOutcome::create([
            'theme_id' => $this->theme->id, 'outcome_code' => 'DEL-LO', 'description' => 'To delete',
        ]);

        $response = $this->deleteJson("/api/v1/learning-outcomes/{$lo->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('learning_outcomes', ['id' => $lo->id]);
    }

    public function test_learning_outcome_validation(): void
    {
        $response = $this->postJson('/api/v1/learning-outcomes', [], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['theme_id', 'description']);
    }

    public function test_list_generic_skills_structure(): void
    {
        $response = $this->getJson('/api/v1/generic-skills', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['*' => ['id', 'skill_name', 'description', 'created_at', 'updated_at']]]);
    }

    public function test_create_generic_skill(): void
    {
        $response = $this->postJson('/api/v1/generic-skills', [
            'skill_name' => 'Critical Thinking',
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['skill_name' => 'Critical Thinking']);
    }

    public function test_update_generic_skill(): void
    {
        $gs = GenericSkill::create(['skill_name' => 'Original Skill']);

        $response = $this->putJson("/api/v1/generic-skills/{$gs->id}", [
            'skill_name' => 'Updated Skill',
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['skill_name' => 'Updated Skill']);
    }

    public function test_delete_generic_skill(): void
    {
        $gs = GenericSkill::create(['skill_name' => 'To Delete']);

        $response = $this->deleteJson("/api/v1/generic-skills/{$gs->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('generic_skills', ['id' => $gs->id]);
    }

    public function test_generic_skill_validation(): void
    {
        $response = $this->postJson('/api/v1/generic-skills', [], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['skill_name']);
    }
}
