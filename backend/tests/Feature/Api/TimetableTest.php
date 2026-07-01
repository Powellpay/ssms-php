<?php

namespace Tests\Feature\Api;

use App\Models\AcademicYear;
use App\Models\ClassLevel;
use App\Models\Role;
use App\Models\Staff;
use App\Models\Stream;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimetableTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private Stream $stream;
    private Subject $subject;
    private Staff $staff;
    private AcademicYear $academicYear;

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
            'year_name' => '2026', 'start_date' => '2026-02-03', 'end_date' => '2026-12-04', 'is_current' => true,
        ]);
        $classLevel = ClassLevel::create(['level_name' => 'Senior 1', 'numeric_level' => 1]);
        $this->stream = Stream::create([
            'class_level_id' => $classLevel->id, 'academic_year_id' => $this->academicYear->id, 'stream_name' => 'A',
        ]);
        $this->subject = Subject::create([
            'subject_code' => 'MTC', 'subject_name' => 'Mathematics', 'category' => 'Core',
        ]);
        $this->staff = Staff::create([
            'staff_no' => 'STF-001', 'first_name' => 'John', 'last_name' => 'Doe',
            'gender' => 'Male', 'email' => 'staff@test.com', 'designation' => 'Teacher', 'status' => 'active',
        ]);
    }

    private function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_can_create_timetable_entry(): void
    {
        $response = $this->postJson('/api/timetable', [
            'stream_id' => $this->stream->id,
            'subject_id' => $this->subject->id,
            'staff_id' => $this->staff->id,
            'academic_year_id' => $this->academicYear->id,
            'day_of_week' => 'Monday',
            'period_no' => 1,
            'start_time' => '08:00',
            'end_time' => '08:40',
        ], $this->authHeaders());

        $response->assertStatus(201);
    }

    public function test_can_list_timetable(): void
    {
        Timetable::create([
            'stream_id' => $this->stream->id, 'subject_id' => $this->subject->id,
            'staff_id' => $this->staff->id, 'academic_year_id' => $this->academicYear->id,
            'day_of_week' => 'Monday', 'period_no' => 1, 'start_time' => '08:00', 'end_time' => '08:40',
        ]);

        $response = $this->getJson('/api/timetable', $this->authHeaders());

        $response->assertStatus(200);
    }

    public function test_can_show_timetable(): void
    {
        $timetable = Timetable::create([
            'stream_id' => $this->stream->id, 'subject_id' => $this->subject->id,
            'staff_id' => $this->staff->id, 'academic_year_id' => $this->academicYear->id,
            'day_of_week' => 'Monday', 'period_no' => 1, 'start_time' => '08:00', 'end_time' => '08:40',
        ]);

        $response = $this->getJson("/api/timetable/{$timetable->id}", $this->authHeaders());

        $response->assertStatus(200);
    }

    public function test_can_delete_timetable(): void
    {
        $timetable = Timetable::create([
            'stream_id' => $this->stream->id, 'subject_id' => $this->subject->id,
            'staff_id' => $this->staff->id, 'academic_year_id' => $this->academicYear->id,
            'day_of_week' => 'Monday', 'period_no' => 1, 'start_time' => '08:00', 'end_time' => '08:40',
        ]);

        $response = $this->deleteJson("/api/timetable/{$timetable->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('timetable', ['id' => $timetable->id]);
    }

    public function test_create_requires_valid_day(): void
    {
        $response = $this->postJson('/api/timetable', [
            'stream_id' => $this->stream->id,
            'subject_id' => $this->subject->id,
            'staff_id' => $this->staff->id,
            'academic_year_id' => $this->academicYear->id,
            'day_of_week' => 'Sunday',
            'period_no' => 1,
            'start_time' => '08:00',
            'end_time' => '08:40',
        ], $this->authHeaders());

        $response->assertStatus(422);
    }
}
