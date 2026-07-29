<?php

namespace Tests\Feature;

use App\Domain\Academic\Models\AcademicYear;
use App\Domain\Academic\Models\ClassLevel;
use App\Domain\Academic\Models\Stream;
use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Models\User;
use App\Domain\Students\Models\Enrollment;
use App\Domain\Students\Models\Guardian;
use App\Domain\Students\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class StudentContractTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private array $studentData;
    private AcademicYear $academicYear;
    private ClassLevel $classLevel;
    private Stream $stream;

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

        $this->studentData = [
            'admission_no' => 'S26-0001',
            'first_name' => 'Faith',
            'last_name' => 'Achieng',
            'gender' => 'Female',
            'dob' => '2012-03-14',
            'admission_date' => '2026-02-03',
            'status' => 'active',
        ];

        $this->academicYear = AcademicYear::create([
            'year_name' => '2026', 'start_date' => '2026-02-03', 'end_date' => '2026-12-04', 'is_current' => true,
        ]);
        $this->classLevel = ClassLevel::create(['level_name' => 'S1', 'numeric_level' => 1]);
        $this->stream = Stream::create([
            'class_level_id' => $this->classLevel->id,
            'academic_year_id' => $this->academicYear->id,
            'stream_name' => 'S1 East',
        ]);
    }

    private function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_list_students_structure(): void
    {
        Student::create($this->studentData);

        $response = $this->getJson('/api/v1/students', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_create_student(): void
    {
        $response = $this->postJson('/api/v1/students', $this->studentData, $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['admission_no' => 'S26-0001']);
    }

    public function test_show_student(): void
    {
        $student = Student::create($this->studentData);

        $response = $this->getJson("/api/v1/students/{$student->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['admission_no' => 'S26-0001']);
    }

    public function test_update_student(): void
    {
        $student = Student::create($this->studentData);

        $response = $this->putJson("/api/v1/students/{$student->id}", [
            'admission_no' => 'S26-0001',
            'first_name' => 'Faith',
            'last_name' => 'Achieng-Okello',
            'gender' => 'Female',
            'admission_date' => '2026-02-03',
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['last_name' => 'Achieng-Okello']);
    }

    public function test_delete_student(): void
    {
        $student = Student::create($this->studentData);

        $response = $this->deleteJson("/api/v1/students/{$student->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    public function test_student_validation(): void
    {
        $response = $this->postJson('/api/v1/students', [], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['admission_no', 'first_name', 'last_name', 'gender', 'admission_date']);
    }

    public function test_import_students_csv(): void
    {
        $csvContent = "first_name,last_name,gender,admission_no,dob,admission_date\nJohn,Doe,Male,STD-001,2012-01-15,2026-02-03\nJane,Smith,Female,STD-002,2013-05-20,2026-02-03";
        $path = tempnam(sys_get_temp_dir(), 'import') . '.csv';
        file_put_contents($path, $csvContent);
        $file = new UploadedFile($path, 'students.csv', 'text/csv', null, true);

        $response = $this->withHeaders($this->authHeaders())
            ->post('/api/v1/students/import', [
                'file' => $file,
                'stream_id' => $this->stream->id,
                'academic_year_id' => $this->academicYear->id,
            ]);

        unlink($path);

        $response->assertStatus(200);
    }

    public function test_download_template(): void
    {
        $response = $this->getJson('/api/v1/students/import/template', $this->authHeaders());

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type') ?? '');
    }

    public function test_import_validation(): void
    {
        $response = $this->postJson('/api/v1/students/import', [], $this->authHeaders());

        $response->assertStatus(422);
    }

    public function test_find_by_admission_no(): void
    {
        Student::create($this->studentData);

        $response = $this->getJson('/api/v1/students/admission/S26-0001', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['admission_no' => 'S26-0001']);
    }

    public function test_find_by_status(): void
    {
        Student::create($this->studentData);
        Student::create([
            'admission_no' => 'S26-0002', 'first_name' => 'Daniel', 'last_name' => 'Wasswa',
            'gender' => 'Male', 'admission_date' => '2026-02-03', 'status' => 'active',
        ]);

        $response = $this->getJson('/api/v1/students/status/active', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_current_enrollment(): void
    {
        $student = Student::create($this->studentData);
        Enrollment::create([
            'student_id' => $student->id,
            'stream_id' => $this->stream->id,
            'academic_year_id' => $this->academicYear->id,
            'enrollment_date' => '2026-02-03',
        ]);

        $response = $this->getJson("/api/v1/students/{$student->id}/enrollment", $this->authHeaders());

        $response->assertStatus(200);
    }

    public function test_list_guardians_structure(): void
    {
        Guardian::create(['first_name' => 'Peter', 'last_name' => 'Lutalo', 'relationship' => 'Father', 'phone' => '0771000001']);
        Guardian::create(['first_name' => 'Mary', 'last_name' => 'Nakato', 'relationship' => 'Mother', 'phone' => '0771000002']);

        $response = $this->getJson('/api/v1/guardians', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['*' => ['id', 'first_name', 'last_name', 'relationship', 'phone', 'email', 'occupation', 'created_at', 'updated_at']]]);
    }

    public function test_create_guardian(): void
    {
        $response = $this->postJson('/api/v1/guardians', [
            'first_name' => 'John', 'last_name' => 'Mukasa', 'relationship' => 'Uncle', 'phone' => '0771000003',
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['first_name' => 'John']);
    }

    public function test_show_guardian(): void
    {
        $guardian = Guardian::create(['first_name' => 'Sarah', 'last_name' => 'Nabatanzi', 'relationship' => 'Mother', 'phone' => '0771000004']);

        $response = $this->getJson("/api/v1/guardians/{$guardian->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['first_name' => 'Sarah']);
    }

    public function test_update_guardian(): void
    {
        $guardian = Guardian::create(['first_name' => 'Tom', 'last_name' => 'Kato', 'relationship' => 'Father', 'phone' => '0771000005']);

        $response = $this->putJson("/api/v1/guardians/{$guardian->id}", [
            'first_name' => 'Tom', 'last_name' => 'Kato', 'relationship' => 'Father', 'phone' => '0771000006',
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['phone' => '0771000006']);
    }

    public function test_delete_guardian(): void
    {
        $guardian = Guardian::create(['first_name' => 'Alice', 'last_name' => 'Nambi', 'relationship' => 'Mother', 'phone' => '0771000007']);

        $response = $this->deleteJson("/api/v1/guardians/{$guardian->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('guardians', ['id' => $guardian->id]);
    }

    public function test_list_enrollments_structure(): void
    {
        $student = Student::create($this->studentData);
        Enrollment::create([
            'student_id' => $student->id, 'stream_id' => $this->stream->id,
            'academic_year_id' => $this->academicYear->id, 'enrollment_date' => '2026-02-03',
        ]);

        $response = $this->getJson('/api/v1/enrollments', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['*' => ['id', 'student_id', 'stream_id', 'academic_year_id', 'enrollment_date', 'status', 'created_at', 'updated_at']]]);
    }

    public function test_create_enrollment(): void
    {
        $student = Student::create($this->studentData);

        $response = $this->postJson('/api/v1/enrollments', [
            'student_id' => $student->id, 'stream_id' => $this->stream->id,
            'academic_year_id' => $this->academicYear->id, 'enrollment_date' => '2026-02-03',
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['student_id' => $student->id]);
    }

    public function test_show_enrollment(): void
    {
        $student = Student::create($this->studentData);
        $enrollment = Enrollment::create([
            'student_id' => $student->id, 'stream_id' => $this->stream->id,
            'academic_year_id' => $this->academicYear->id, 'enrollment_date' => '2026-02-03',
        ]);

        $response = $this->getJson("/api/v1/enrollments/{$enrollment->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['student_id' => $student->id]);
    }

    public function test_delete_enrollment(): void
    {
        $student = Student::create($this->studentData);
        $enrollment = Enrollment::create([
            'student_id' => $student->id, 'stream_id' => $this->stream->id,
            'academic_year_id' => $this->academicYear->id, 'enrollment_date' => '2026-02-03',
        ]);

        $response = $this->deleteJson("/api/v1/enrollments/{$enrollment->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('enrollments', ['id' => $enrollment->id]);
    }
}
