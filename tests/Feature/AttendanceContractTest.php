<?php

namespace Tests\Feature;

use App\Domain\Academic\Models\AcademicYear;
use App\Domain\Academic\Models\ClassLevel;
use App\Domain\Academic\Models\Stream;
use App\Domain\Academic\Models\Term;
use App\Domain\Attendance\Models\Attendance;
use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Models\User;
use App\Domain\Staff\Models\Staff;
use App\Domain\Students\Models\Enrollment;
use App\Domain\Students\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceContractTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private Term $term;
    private Stream $stream;
    private Student $student1;
    private Student $student2;

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
        $this->stream = Stream::create([
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
        Enrollment::create([
            'student_id' => $this->student1->id,
            'stream_id' => $this->stream->id,
            'academic_year_id' => $academicYear->id,
            'enrollment_date' => '2026-02-03',
            'status' => 'active',
        ]);
        Enrollment::create([
            'student_id' => $this->student2->id,
            'stream_id' => $this->stream->id,
            'academic_year_id' => $academicYear->id,
            'enrollment_date' => '2026-02-03',
            'status' => 'active',
        ]);
    }

    private function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_list_attendance_structure(): void
    {
        Attendance::create([
            'student_id' => $this->student1->id, 'term_id' => $this->term->id,
            'attendance_date' => now()->format('Y-m-d'), 'status' => 'Present',
        ]);

        $response = $this->getJson('/api/v1/attendance', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'student_id',
                        'term_id',
                        'attendance_date',
                        'status',
                        'recorded_by',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_create_attendance(): void
    {
        $response = $this->postJson('/api/v1/attendance', [
            'student_id' => $this->student1->id,
            'term_id' => $this->term->id,
            'attendance_date' => now()->format('Y-m-d'),
            'status' => 'Present',
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'student_id',
                'term_id',
                'attendance_date',
                'status',
                'recorded_by',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_bulk_register_attendance(): void
    {
        $date = now()->format('Y-m-d');

        $response = $this->postJson('/api/v1/attendance/register', [
            'term_id' => $this->term->id,
            'attendance_date' => $date,
            'records' => [
                ['student_id' => $this->student1->id, 'status' => 'Present'],
                ['student_id' => $this->student2->id, 'status' => 'Absent'],
            ],
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'count'])
            ->assertJsonFragment(['count' => 2]);

        $this->assertDatabaseHas('attendance', [
            'student_id' => $this->student1->id,
            'attendance_date' => $date,
            'status' => 'Present',
        ]);
        $this->assertDatabaseHas('attendance', [
            'student_id' => $this->student2->id,
            'attendance_date' => $date,
            'status' => 'Absent',
        ]);
    }

    public function test_get_attendance_register(): void
    {
        $date = now()->format('Y-m-d');

        Attendance::create([
            'student_id' => $this->student1->id, 'term_id' => $this->term->id,
            'attendance_date' => $date, 'status' => 'Present',
        ]);

        $response = $this->getJson('/api/v1/attendance/register?' . http_build_query([
            'term_id' => $this->term->id,
            'attendance_date' => $date,
            'stream_id' => $this->stream->id,
        ]), $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'student_id',
                    'first_name',
                    'last_name',
                    'admission_no',
                    'status',
                ],
            ]);

        $response->assertJsonFragment(['student_id' => $this->student1->id, 'status' => 'Present']);
        $response->assertJsonFragment(['student_id' => $this->student2->id, 'status' => null]);
    }

    public function test_attendance_validation_invalid_status(): void
    {
        $response = $this->postJson('/api/v1/attendance', [
            'student_id' => $this->student1->id,
            'term_id' => $this->term->id,
            'attendance_date' => now()->format('Y-m-d'),
            'status' => 'Unknown',
        ], $this->authHeaders());

        $response->assertStatus(422);
    }

    public function test_attendance_validation_missing_student_id(): void
    {
        $response = $this->postJson('/api/v1/attendance', [
            'term_id' => $this->term->id,
            'attendance_date' => now()->format('Y-m-d'),
            'status' => 'Present',
        ], $this->authHeaders());

        $response->assertStatus(422);
    }

    public function test_show_attendance(): void
    {
        $attendance = Attendance::create([
            'student_id' => $this->student1->id, 'term_id' => $this->term->id,
            'attendance_date' => now()->format('Y-m-d'), 'status' => 'Present',
        ]);

        $response = $this->getJson("/api/v1/attendance/{$attendance->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'student_id',
                'term_id',
                'attendance_date',
                'status',
                'recorded_by',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_delete_attendance(): void
    {
        $attendance = Attendance::create([
            'student_id' => $this->student1->id, 'term_id' => $this->term->id,
            'attendance_date' => now()->format('Y-m-d'), 'status' => 'Present',
        ]);

        $response = $this->deleteJson("/api/v1/attendance/{$attendance->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('attendance', ['id' => $attendance->id]);
    }
}
