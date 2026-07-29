<?php

namespace Tests\Feature;

use App\Domain\Academic\Models\AcademicYear;
use App\Domain\Academic\Models\Term;
use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Models\User;
use App\Domain\Discipline\Models\DisciplineRecord;
use App\Domain\Staff\Models\Staff;
use App\Domain\Students\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisciplineContractTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private Student $student;
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
        $this->student = Student::create([
            'admission_no' => 'S26-0001', 'first_name' => 'Faith', 'last_name' => 'Achieng',
            'gender' => 'Female', 'admission_date' => '2026-02-03',
        ]);
        Staff::create([
            'staff_no' => 'STF-001', 'first_name' => 'John', 'last_name' => 'Doe',
            'gender' => 'Male', 'email' => 'staff@test.com', 'designation' => 'Teacher', 'status' => 'active',
        ]);
    }

    private function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_list_discipline_records_structure(): void
    {
        DisciplineRecord::create([
            'student_id' => $this->student->id, 'term_id' => $this->term->id,
            'incident_date' => now()->format('Y-m-d'), 'description' => 'Fighting',
        ]);

        $response = $this->getJson('/api/v1/discipline-records', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'student_id',
                        'term_id',
                        'incident_date',
                        'description',
                        'action_taken',
                        'recorded_by',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_create_discipline_record(): void
    {
        $response = $this->postJson('/api/v1/discipline-records', [
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'incident_date' => now()->format('Y-m-d'),
            'description' => 'Fighting',
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'student_id',
                'term_id',
                'incident_date',
                'description',
                'action_taken',
                'recorded_by',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_show_discipline_record(): void
    {
        $record = DisciplineRecord::create([
            'student_id' => $this->student->id, 'term_id' => $this->term->id,
            'incident_date' => now()->format('Y-m-d'), 'description' => 'Fighting',
        ]);

        $response = $this->getJson("/api/v1/discipline-records/{$record->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'student_id',
                'term_id',
                'incident_date',
                'description',
                'action_taken',
                'recorded_by',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_update_discipline_record(): void
    {
        $record = DisciplineRecord::create([
            'student_id' => $this->student->id, 'term_id' => $this->term->id,
            'incident_date' => now()->format('Y-m-d'), 'description' => 'Fighting',
        ]);

        $response = $this->putJson("/api/v1/discipline-records/{$record->id}", [
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'incident_date' => now()->format('Y-m-d'),
            'description' => 'Bullying',
            'action_taken' => 'Suspended',
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'student_id',
                'term_id',
                'incident_date',
                'description',
                'action_taken',
                'recorded_by',
                'created_at',
                'updated_at',
            ])
            ->assertJsonFragment(['description' => 'Bullying']);
    }

    public function test_delete_discipline_record(): void
    {
        $record = DisciplineRecord::create([
            'student_id' => $this->student->id, 'term_id' => $this->term->id,
            'incident_date' => now()->format('Y-m-d'), 'description' => 'Fighting',
        ]);

        $response = $this->deleteJson("/api/v1/discipline-records/{$record->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('discipline_records', ['id' => $record->id]);
    }

    public function test_discipline_validation_missing_fields(): void
    {
        $response = $this->postJson('/api/v1/discipline-records', [
            'term_id' => $this->term->id,
            'incident_date' => now()->format('Y-m-d'),
        ], $this->authHeaders());

        $response->assertStatus(422);
    }
}
