<?php

namespace Tests\Feature\Api;

use App\Domain\Academic\Models\AcademicYear;
use App\Domain\Academic\Models\ClassLevel;
use App\Domain\Auth\Models\Role;
use App\Domain\Staff\Models\Staff;
use App\Domain\Academic\Models\Stream;
use App\Domain\Curriculum\Models\Subject;
use App\Domain\Curriculum\Models\SubjectTeacher;
use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectTeacherTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private AcademicYear $academicYear;
    private ClassLevel $classLevel;
    private Stream $stream;
    private Subject $subject;
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

        $this->subject = Subject::create([
            'subject_code' => 'MTC',
            'subject_name' => 'Mathematics',
            'category' => 'Core',
        ]);

        $this->staff = Staff::create([
            'staff_no' => 'STF-001',
            'first_name' => 'John',
            'last_name' => 'Mukasa',
            'gender' => 'Male',
            'status' => 'active',
        ]);
    }

    private function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_can_create_subject_teacher(): void
    {
        $response = $this->postJson('/api/subject-teachers', [
            'subject_id' => $this->subject->id,
            'stream_id' => $this->stream->id,
            'staff_id' => $this->staff->id,
            'academic_year_id' => $this->academicYear->id,
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['subject_id' => $this->subject->id]);
    }

    public function test_can_list_subject_teachers(): void
    {
        SubjectTeacher::create([
            'subject_id' => $this->subject->id,
            'stream_id' => $this->stream->id,
            'staff_id' => $this->staff->id,
            'academic_year_id' => $this->academicYear->id,
        ]);

        $response = $this->getJson('/api/subject-teachers', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_delete_subject_teacher(): void
    {
        $subjectTeacher = SubjectTeacher::create([
            'subject_id' => $this->subject->id,
            'stream_id' => $this->stream->id,
            'staff_id' => $this->staff->id,
            'academic_year_id' => $this->academicYear->id,
        ]);

        $response = $this->deleteJson("/api/subject-teachers/{$subjectTeacher->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('subject_teachers', ['id' => $subjectTeacher->id]);
    }
}
