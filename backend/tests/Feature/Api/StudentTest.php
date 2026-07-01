<?php

namespace Tests\Feature\Api;

use App\Models\AcademicYear;
use App\Models\ClassLevel;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private array $studentData;

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
    }

    private function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_can_list_students(): void
    {
        Student::create($this->studentData);

        $response = $this->getJson('/api/students', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_create_student(): void
    {
        $response = $this->postJson('/api/students', $this->studentData, $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['admission_no' => 'S26-0001']);
    }

    public function test_can_show_student(): void
    {
        $student = Student::create($this->studentData);

        $response = $this->getJson("/api/students/{$student->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['first_name' => 'Faith']);
    }

    public function test_can_update_student(): void
    {
        $student = Student::create($this->studentData);

        $response = $this->putJson("/api/students/{$student->id}", [
            'admission_no' => 'S26-0001',
            'first_name' => 'Faith',
            'last_name' => 'Achieng-Okello',
            'gender' => 'Female',
            'admission_date' => '2026-02-03',
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['last_name' => 'Achieng-Okello']);
    }

    public function test_can_delete_student(): void
    {
        $student = Student::create($this->studentData);

        $response = $this->deleteJson("/api/students/{$student->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    public function test_create_requires_unique_admission_no(): void
    {
        Student::create($this->studentData);

        $response = $this->postJson('/api/students', $this->studentData, $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['admission_no']);
    }

    public function test_create_requires_required_fields(): void
    {
        $response = $this->postJson('/api/students', [], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['admission_no', 'first_name', 'last_name', 'gender', 'admission_date']);
    }

    public function test_delete_returns_message(): void
    {
        $student = Student::create($this->studentData);

        $response = $this->deleteJson("/api/students/{$student->id}", [], $this->authHeaders());

        $response->assertJson(['message' => 'Deleted']);
    }

    public function test_can_find_by_admission_no(): void
    {
        Student::create($this->studentData);

        $response = $this->getJson('/api/students/admission/S26-0001', $this->authHeaders());

        $response->assertStatus(200);
    }

    public function test_can_filter_by_status(): void
    {
        Student::create($this->studentData);
        Student::create([
            'admission_no' => 'S26-0002',
            'first_name' => 'Daniel',
            'last_name' => 'Wasswa',
            'gender' => 'Male',
            'admission_date' => '2026-02-03',
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/students/status/active', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_unauthenticated_request_fails(): void
    {
        $response = $this->getJson('/api/students');

        $response->assertStatus(401);
    }
}
