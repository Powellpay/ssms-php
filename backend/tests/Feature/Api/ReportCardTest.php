<?php

namespace Tests\Feature\Api;

use App\Models\AcademicYear;
use App\Models\ClassLevel;
use App\Models\ReportCard;
use App\Models\Role;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Term;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportCardTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private Student $student;
    private Term $term;
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

        $academicYear = AcademicYear::create([
            'year_name' => '2026', 'start_date' => '2026-02-03', 'end_date' => '2026-12-04',
        ]);
        $this->term = Term::create([
            'academic_year_id' => $academicYear->id, 'term_name' => 'Term 1',
            'start_date' => '2026-02-03', 'end_date' => '2026-05-08',
        ]);
        $classLevel = ClassLevel::create([
            'level_name' => 'Grade 7', 'numeric_level' => 7,
        ]);
        $this->stream = Stream::create([
            'class_level_id' => $classLevel->id, 'academic_year_id' => $academicYear->id,
            'stream_name' => 'East',
        ]);
        $this->student = Student::create([
            'admission_no' => 'S26-0001', 'first_name' => 'Faith', 'last_name' => 'Achieng',
            'gender' => 'Female', 'admission_date' => '2026-02-03',
        ]);
    }

    private function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_can_create_report_card(): void
    {
        $response = $this->postJson('/api/report-cards', [
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'stream_id' => $this->stream->id,
            'days_present' => 58,
            'days_absent' => 2,
        ], $this->authHeaders());

        $response->assertStatus(201);
    }

    public function test_can_list_report_cards(): void
    {
        $response = $this->getJson('/api/report-cards', $this->authHeaders());

        $response->assertStatus(200);
    }

    public function test_can_show_report_card(): void
    {
        $card = ReportCard::create([
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'stream_id' => $this->stream->id,
            'days_present' => 58,
            'days_absent' => 2,
        ]);

        $response = $this->getJson("/api/report-cards/{$card->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['days_present' => 58]);
    }

    public function test_can_delete_report_card(): void
    {
        $card = ReportCard::create([
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'stream_id' => $this->stream->id,
            'days_present' => 58,
            'days_absent' => 2,
        ]);

        $response = $this->deleteJson("/api/report-cards/{$card->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('report_cards', ['id' => $card->id]);
    }
}
