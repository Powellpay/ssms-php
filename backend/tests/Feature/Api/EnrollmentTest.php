<?php

namespace Tests\Feature\Api;

use App\Models\AcademicYear;
use App\Models\ClassLevel;
use App\Models\Enrollment;
use App\Models\Role;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Term;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private AcademicYear $academicYear;
    private ClassLevel $classLevel;
    private Stream $stream;
    private Student $student;

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

        $this->academicYear = AcademicYear::create([
            'year_name' => '2026',
            'start_date' => '2026-02-03',
            'end_date' => '2026-12-04',
            'is_current' => true,
        ]);

        $this->classLevel = ClassLevel::create(['level_name' => 'S1', 'numeric_level' => 1]);

        $this->stream = Stream::create([
            'class_level_id' => $this->classLevel->id,
            'academic_year_id' => $this->academicYear->id,
            'stream_name' => 'S1 East',
        ]);

        $this->student = Student::create([
            'admission_no' => 'S26-0001',
            'first_name' => 'Faith',
            'last_name' => 'Achieng',
            'gender' => 'Female',
            'admission_date' => '2026-02-03',
            'status' => 'active',
        ]);
    }

    private function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_can_create_enrollment(): void
    {
        $response = $this->postJson('/api/enrollments', [
            'student_id' => $this->student->id,
            'stream_id' => $this->stream->id,
            'academic_year_id' => $this->academicYear->id,
            'enrollment_date' => '2026-02-03',
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['student_id' => $this->student->id]);
    }

    public function test_can_list_enrollments(): void
    {
        Enrollment::create([
            'student_id' => $this->student->id,
            'stream_id' => $this->stream->id,
            'academic_year_id' => $this->academicYear->id,
            'enrollment_date' => '2026-02-03',
        ]);

        $response = $this->getJson('/api/enrollments', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_show_enrollment(): void
    {
        $enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'stream_id' => $this->stream->id,
            'academic_year_id' => $this->academicYear->id,
            'enrollment_date' => '2026-02-03',
        ]);

        $response = $this->getJson("/api/enrollments/{$enrollment->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['student_id' => $this->student->id]);
    }

    public function test_can_delete_enrollment(): void
    {
        $enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'stream_id' => $this->stream->id,
            'academic_year_id' => $this->academicYear->id,
            'enrollment_date' => '2026-02-03',
        ]);

        $response = $this->deleteJson("/api/enrollments/{$enrollment->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('enrollments', ['id' => $enrollment->id]);
    }

    public function test_create_requires_unique_student_per_year(): void
    {
        Enrollment::create([
            'student_id' => $this->student->id,
            'stream_id' => $this->stream->id,
            'academic_year_id' => $this->academicYear->id,
            'enrollment_date' => '2026-02-03',
        ]);

        $response = $this->postJson('/api/enrollments', [
            'student_id' => $this->student->id,
            'stream_id' => $this->stream->id,
            'academic_year_id' => $this->academicYear->id,
            'enrollment_date' => '2026-02-03',
        ], $this->authHeaders());

        $response->assertStatus(422);
    }
}
