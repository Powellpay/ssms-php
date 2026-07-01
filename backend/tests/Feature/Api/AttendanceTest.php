<?php

namespace Tests\Feature\Api;

use App\Domain\Academic\Models\AcademicYear;
use App\Domain\Attendance\Models\Attendance;
use App\Domain\Academic\Models\ClassLevel;
use App\Domain\Auth\Models\Role;
use App\Domain\Staff\Models\Staff;
use App\Domain\Academic\Models\Stream;
use App\Domain\Students\Models\Student;
use App\Domain\Academic\Models\Term;
use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private Student $student1;
    private Student $student2;
    private Term $term;

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
            'year_name' => '2026', 'start_date' => '2026-02-03', 'end_date' => '2026-12-04', 'is_current' => true,
        ]);
        $this->term = Term::create([
            'academic_year_id' => $academicYear->id, 'term_name' => 'Term 1',
            'start_date' => '2026-02-03', 'end_date' => '2026-05-08', 'is_current' => true,
        ]);
        $classLevel = ClassLevel::create(['level_name' => 'Senior 1', 'numeric_level' => 1]);
        $stream = Stream::create([
            'class_level_id' => $classLevel->id, 'academic_year_id' => $academicYear->id, 'stream_name' => 'A',
        ]);
        Staff::create([
            'staff_no' => 'STF-001', 'first_name' => 'John', 'last_name' => 'Doe',
            'gender' => 'Male', 'email' => 'staff@test.com', 'designation' => 'Teacher', 'status' => 'active',
        ]);
        $this->student1 = Student::create([
            'admission_no' => 'S26-0001', 'first_name' => 'Faith', 'last_name' => 'Achieng',
            'gender' => 'Female', 'admission_date' => '2026-02-03',
        ]);
        $this->student2 = Student::create([
            'admission_no' => 'S26-0002', 'first_name' => 'Daniel', 'last_name' => 'Wasswa',
            'gender' => 'Male', 'admission_date' => '2026-02-03',
        ]);
    }

    private function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_can_mark_attendance(): void
    {
        $response = $this->postJson('/api/attendance', [
            'student_id' => $this->student1->id,
            'term_id' => $this->term->id,
            'attendance_date' => now()->format('Y-m-d'),
            'status' => 'Present',
        ], $this->authHeaders());

        $response->assertStatus(201);
    }

    public function test_can_list_attendance(): void
    {
        Attendance::create([
            'student_id' => $this->student1->id, 'term_id' => $this->term->id,
            'attendance_date' => now()->format('Y-m-d'), 'status' => 'Present',
        ]);

        $response = $this->getJson('/api/attendance', $this->authHeaders());

        $response->assertStatus(200);
    }

    public function test_can_show_attendance(): void
    {
        $attendance = Attendance::create([
            'student_id' => $this->student1->id, 'term_id' => $this->term->id,
            'attendance_date' => now()->format('Y-m-d'), 'status' => 'Present',
        ]);

        $response = $this->getJson("/api/attendance/{$attendance->id}", $this->authHeaders());

        $response->assertStatus(200);
    }

    public function test_can_update_attendance(): void
    {
        $attendance = Attendance::create([
            'student_id' => $this->student1->id, 'term_id' => $this->term->id,
            'attendance_date' => now()->format('Y-m-d'), 'status' => 'Present',
        ]);

        $response = $this->putJson("/api/attendance/{$attendance->id}", [
            'student_id' => $this->student1->id,
            'term_id' => $this->term->id,
            'attendance_date' => now()->format('Y-m-d'),
            'status' => 'Absent',
        ], $this->authHeaders());

        $response->assertStatus(200);
    }

    public function test_can_delete_attendance(): void
    {
        $attendance = Attendance::create([
            'student_id' => $this->student1->id, 'term_id' => $this->term->id,
            'attendance_date' => now()->format('Y-m-d'), 'status' => 'Present',
        ]);

        $response = $this->deleteJson("/api/attendance/{$attendance->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('attendance', ['id' => $attendance->id]);
    }

    public function test_create_requires_valid_status(): void
    {
        $response = $this->postJson('/api/attendance', [
            'student_id' => $this->student1->id,
            'term_id' => $this->term->id,
            'attendance_date' => now()->format('Y-m-d'),
            'status' => 'Unknown',
        ], $this->authHeaders());

        $response->assertStatus(422);
    }

    public function test_create_requires_unique_per_day(): void
    {
        Attendance::create([
            'student_id' => $this->student1->id, 'term_id' => $this->term->id,
            'attendance_date' => now()->format('Y-m-d'), 'status' => 'Present',
        ]);

        $response = $this->postJson('/api/attendance', [
            'student_id' => $this->student1->id,
            'term_id' => $this->term->id,
            'attendance_date' => now()->format('Y-m-d'),
            'status' => 'Absent',
        ], $this->authHeaders());

        $this->assertContains($response->status(), [422, 500]);
    }
}
