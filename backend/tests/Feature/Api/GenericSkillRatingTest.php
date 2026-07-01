<?php

namespace Tests\Feature\Api;

use App\Domain\Curriculum\Models\GenericSkill;
use App\Domain\Assessment\Models\GenericSkillRating;
use App\Domain\Auth\Models\Role;
use App\Domain\Assessment\Models\SkillRatingScale;
use App\Domain\Staff\Models\Staff;
use App\Domain\Students\Models\Student;
use App\Domain\Academic\Models\Term;
use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenericSkillRatingTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private Student $student;
    private Term $term;
    private GenericSkill $genericSkill;
    private SkillRatingScale $rating;
    private Staff $staff;

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

        $academicYear = \App\Domain\Academic\Models\AcademicYear::create([
            'year_name' => '2026', 'start_date' => '2026-02-03', 'end_date' => '2026-12-04',
        ]);
        $this->term = Term::create([
            'academic_year_id' => $academicYear->id, 'term_name' => 'Term 1',
            'start_date' => '2026-02-03', 'end_date' => '2026-05-08',
        ]);
        $this->student = Student::create([
            'admission_no' => 'S26-0001', 'first_name' => 'Faith', 'last_name' => 'Achieng',
            'gender' => 'Female', 'admission_date' => '2026-02-03',
        ]);
        $this->genericSkill = GenericSkill::create([
            'skill_name' => 'Communication',
        ]);
        $this->rating = SkillRatingScale::create([
            'rating_code' => 'BEG', 'rating_label' => 'Beginning', 'rating_value' => 1,
        ]);
        $this->staff = Staff::create([
            'staff_no' => 'STF-001', 'first_name' => 'Jane', 'last_name' => 'Doe',
            'gender' => 'Female', 'status' => 'active',
        ]);
    }

    private function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_can_create_generic_skill_rating(): void
    {
        $response = $this->postJson('/api/generic-skill-ratings', [
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'generic_skill_id' => $this->genericSkill->id,
            'rating_id' => $this->rating->id,
            'recorded_by' => $this->staff->id,
        ], $this->authHeaders());

        $response->assertStatus(201);
    }

    public function test_can_list_generic_skill_ratings(): void
    {
        $response = $this->getJson('/api/generic-skill-ratings', $this->authHeaders());

        $response->assertStatus(200);
    }

    public function test_can_delete_generic_skill_rating(): void
    {
        $gsr = GenericSkillRating::create([
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'generic_skill_id' => $this->genericSkill->id,
            'rating_id' => $this->rating->id,
            'recorded_by' => $this->staff->id,
        ]);

        $response = $this->deleteJson("/api/generic-skill-ratings/{$gsr->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('generic_skill_ratings', ['id' => $gsr->id]);
    }
}
