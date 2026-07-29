<?php

namespace Tests\Feature\Api;

use App\Domain\Academic\Models\AcademicYear;
use App\Domain\Assessment\Models\AssessmentRecord;
use App\Domain\Assessment\Models\AssessmentType;
use App\Domain\Auth\Models\Role;
use App\Domain\Staff\Models\Staff;
use App\Domain\Students\Models\Student;
use App\Domain\Curriculum\Models\Subject;
use App\Domain\Academic\Models\Term;
use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentRecordTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private Student $student;
    private Subject $subject;
    private AssessmentType $assessmentType;
    private Term $term;
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

        $academicYear = AcademicYear::create([
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
        $this->subject = Subject::create([
            'subject_code' => 'MTC', 'subject_name' => 'Mathematics', 'category' => 'Core',
        ]);
        $this->assessmentType = AssessmentType::create([
            'type_name' => 'Continuous Assessment Test', 'category' => 'Formative',
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

    public function test_can_create_assessment_record(): void
    {
        $response = $this->postJson('/api/v1/assessment-records', [
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'assessment_type_id' => $this->assessmentType->id,
            'term_id' => $this->term->id,
            'score' => 75,
            'date_recorded' => '2026-03-15',
            'recorded_by' => $this->staff->id,
        ], $this->authHeaders());

        $response->assertStatus(201);
    }

    public function test_can_list_assessment_records(): void
    {
        $response = $this->getJson('/api/v1/assessment-records', $this->authHeaders());

        $response->assertStatus(200);
    }

    public function test_can_show_assessment_record(): void
    {
        $record = AssessmentRecord::create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'assessment_type_id' => $this->assessmentType->id,
            'term_id' => $this->term->id,
            'score' => 75,
            'date_recorded' => '2026-03-15',
            'recorded_by' => $this->staff->id,
        ]);

        $response = $this->getJson("/api/v1/assessment-records/{$record->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['score' => '75.00']);
    }

    public function test_can_delete_assessment_record(): void
    {
        $record = AssessmentRecord::create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'assessment_type_id' => $this->assessmentType->id,
            'term_id' => $this->term->id,
            'score' => 60,
            'date_recorded' => '2026-03-15',
            'recorded_by' => $this->staff->id,
        ]);

        $response = $this->deleteJson("/api/v1/assessment-records/{$record->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('assessment_records', ['id' => $record->id]);
    }

    public function test_create_requires_valid_student(): void
    {
        $response = $this->postJson('/api/v1/assessment-records', [
            'student_id' => 999,
            'subject_id' => $this->subject->id,
            'assessment_type_id' => $this->assessmentType->id,
            'term_id' => $this->term->id,
            'score' => 75,
            'date_recorded' => '2026-03-15',
            'recorded_by' => $this->staff->id,
        ], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['student_id']);
    }
}
