<?php

namespace Tests\Feature\Api;

use App\Models\AcademicYear;
use App\Models\ClassLevel;
use App\Models\GradingScale;
use App\Models\Role;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectTermResultTest extends TestCase
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

        GradingScale::insert([
            ['grade' => 'A', 'descriptor' => 'Exceptional', 'min_score' => 80, 'max_score' => 100, 'remarks' => ''],
            ['grade' => 'B', 'descriptor' => 'Outstanding', 'min_score' => 70, 'max_score' => 79.99, 'remarks' => ''],
            ['grade' => 'C', 'descriptor' => 'Satisfactory', 'min_score' => 55, 'max_score' => 69.99, 'remarks' => ''],
            ['grade' => 'D', 'descriptor' => 'Basic', 'min_score' => 40, 'max_score' => 54.99, 'remarks' => ''],
            ['grade' => 'E', 'descriptor' => 'Elementary', 'min_score' => 0, 'max_score' => 39.99, 'remarks' => ''],
        ]);
    }

    private function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_can_create_subject_term_result(): void
    {
        $academicYear = AcademicYear::create([
            'year_name' => '2026', 'start_date' => '2026-02-03', 'end_date' => '2026-12-04', 'is_current' => true,
        ]);
        $term = Term::create([
            'academic_year_id' => $academicYear->id, 'term_name' => 'Term 1',
            'start_date' => '2026-02-03', 'end_date' => '2026-05-08', 'is_current' => true,
        ]);
        $student = Student::create([
            'admission_no' => 'S26-0001', 'first_name' => 'Faith', 'last_name' => 'Achieng',
            'gender' => 'Female', 'admission_date' => '2026-02-03',
        ]);
        $subject = Subject::create([
            'subject_code' => 'MTC', 'subject_name' => 'Mathematics', 'category' => 'Core',
        ]);

        $response = $this->postJson('/api/subject-term-results', [
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'term_id' => $term->id,
            'ca_score' => 16.0,
            'eot_score' => 60.0,
        ], $this->authHeaders());

        $response->assertStatus(201);
    }

    public function test_can_list_subject_term_results(): void
    {
        $response = $this->getJson('/api/subject-term-results', $this->authHeaders());

        $response->assertStatus(200);
    }

    public function test_can_show_subject_term_result(): void
    {
        $academicYear = AcademicYear::create([
            'year_name' => '2026', 'start_date' => '2026-02-03', 'end_date' => '2026-12-04',
        ]);
        $term = Term::create([
            'academic_year_id' => $academicYear->id, 'term_name' => 'Term 1',
            'start_date' => '2026-02-03', 'end_date' => '2026-05-08',
        ]);
        $student = Student::create([
            'admission_no' => 'S26-0002', 'first_name' => 'Daniel', 'last_name' => 'Wasswa',
            'gender' => 'Male', 'admission_date' => '2026-02-03',
        ]);
        $subject = Subject::create([
            'subject_code' => 'ENG', 'subject_name' => 'English', 'category' => 'Core',
        ]);

        $result = \App\Models\SubjectTermResult::create([
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'term_id' => $term->id,
            'ca_score' => 16.0,
            'eot_score' => 60.0,
            'final_score' => 76.0,
            'final_grade' => 'B',
        ]);

        $response = $this->getJson("/api/subject-term-results/{$result->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['final_grade' => 'B']);
    }

    public function test_can_delete_subject_term_result(): void
    {
        $academicYear = AcademicYear::create([
            'year_name' => '2026', 'start_date' => '2026-02-03', 'end_date' => '2026-12-04',
        ]);
        $term = Term::create([
            'academic_year_id' => $academicYear->id, 'term_name' => 'Term 1',
            'start_date' => '2026-02-03', 'end_date' => '2026-05-08',
        ]);
        $student = Student::create([
            'admission_no' => 'S26-0003', 'first_name' => 'Test', 'last_name' => 'Student',
            'gender' => 'Female', 'admission_date' => '2026-02-03',
        ]);
        $subject = Subject::create([
            'subject_code' => 'BIO', 'subject_name' => 'Biology', 'category' => 'Core',
        ]);

        $result = \App\Models\SubjectTermResult::create([
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'term_id' => $term->id,
            'ca_score' => 14.0,
            'eot_score' => 50.0,
            'final_score' => 64.0,
            'final_grade' => 'C',
        ]);

        $response = $this->deleteJson("/api/subject-term-results/{$result->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('subject_term_results', ['id' => $result->id]);
    }
}
