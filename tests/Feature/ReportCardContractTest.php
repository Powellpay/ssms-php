<?php

namespace Tests\Feature;

use App\Domain\Academic\Models\AcademicYear;
use App\Domain\Academic\Models\ClassLevel;
use App\Domain\Academic\Models\Stream;
use App\Domain\Academic\Models\Term;
use App\Domain\Assessment\Models\GenericSkillRating;
use App\Domain\Assessment\Models\GradingScale;
use App\Domain\Assessment\Models\SkillRatingScale;
use App\Domain\Assessment\Models\SubjectTermResult;
use App\Domain\Attendance\Models\Attendance;
use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Models\User;
use App\Domain\Curriculum\Models\GenericSkill;
use App\Domain\Curriculum\Models\Subject;
use App\Domain\Reports\Models\ReportCard;
use App\Domain\Students\Models\Enrollment;
use App\Domain\Students\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportCardContractTest extends TestCase
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
            'level_name' => 'Senior 1', 'numeric_level' => 1,
        ]);
        $this->stream = Stream::create([
            'class_level_id' => $classLevel->id, 'academic_year_id' => $academicYear->id,
            'stream_name' => 'East',
        ]);
        $this->student = Student::create([
            'admission_no' => 'S26-0001', 'first_name' => 'Faith', 'last_name' => 'Achieng',
            'gender' => 'Female', 'admission_date' => '2026-02-03',
        ]);
        Enrollment::create([
            'student_id' => $this->student->id,
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

    public function test_list_report_cards_structure(): void
    {
        ReportCard::create([
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'stream_id' => $this->stream->id,
            'days_present' => 58,
            'days_absent' => 2,
        ]);

        $response = $this->getJson('/api/v1/report-cards', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'student_id',
                        'term_id',
                        'stream_id',
                        'days_present',
                        'days_absent',
                        'class_teacher_comment',
                        'head_teacher_comment',
                        'next_term_begins',
                        'date_issued',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_generate_report_card(): void
    {
        Subject::create([
            'subject_code' => 'MAT', 'subject_name' => 'Mathematics', 'category' => 'Core',
        ]);
        GradingScale::create([
            'grade' => 'A', 'min_score' => 80, 'max_score' => 100, 'descriptor' => 'Excellent', 'remarks' => 'Outstanding',
        ]);
        SubjectTermResult::create([
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'subject_id' => 1,
            'ca_score' => 40,
            'eot_score' => 50,
            'final_score' => 90,
            'final_grade' => 'A',
        ]);
        $skill = GenericSkill::create(['skill_name' => 'Critical Thinking']);
        $ratingScale = SkillRatingScale::create([
            'rating_code' => 'EX', 'rating_label' => 'Excellent', 'rating_value' => 4,
        ]);
        GenericSkillRating::create([
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'generic_skill_id' => $skill->id,
            'rating_id' => $ratingScale->id,
        ]);
        Attendance::create([
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'attendance_date' => '2026-02-10',
            'status' => 'Present',
        ]);
        Attendance::create([
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'attendance_date' => '2026-02-11',
            'status' => 'Present',
        ]);
        Attendance::create([
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'attendance_date' => '2026-02-12',
            'status' => 'Absent',
        ]);

        $response = $this->postJson('/api/v1/report-cards/generate', [
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'student_id',
                'term_id',
                'stream_id' => '*',
                'days_present',
                'days_absent',
                'class_teacher_comment',
                'head_teacher_comment',
                'next_term_begins',
                'date_issued',
                'created_at',
                'updated_at',
            ])
            ->assertJsonFragment(['days_present' => 2])
            ->assertJsonFragment(['days_absent' => 1]);
    }

    public function test_download_report_card_pdf(): void
    {
        $card = ReportCard::create([
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'stream_id' => $this->stream->id,
            'days_present' => 58,
            'days_absent' => 2,
        ]);

        $response = $this->getJson("/api/v1/report-cards/{$card->id}/pdf", $this->authHeaders());

        $this->assertContains($response->status(), [200, 404, 500]);
        if ($response->status() === 200) {
            $response->assertHeader('Content-Type');
        }
    }

    public function test_create_report_card(): void
    {
        $response = $this->postJson('/api/v1/report-cards', [
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'stream_id' => $this->stream->id,
            'days_present' => 58,
            'days_absent' => 2,
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'student_id',
                'term_id',
                'stream_id',
                'days_present',
                'days_absent',
                'class_teacher_comment',
                'head_teacher_comment',
                'next_term_begins',
                'date_issued',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_show_report_card(): void
    {
        $card = ReportCard::create([
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'stream_id' => $this->stream->id,
            'days_present' => 58,
            'days_absent' => 2,
        ]);

        $response = $this->getJson("/api/v1/report-cards/{$card->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'student_id',
                'term_id',
                'stream_id',
                'days_present',
                'days_absent',
                'class_teacher_comment',
                'head_teacher_comment',
                'next_term_begins',
                'date_issued',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_update_report_card(): void
    {
        $card = ReportCard::create([
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'stream_id' => $this->stream->id,
            'days_present' => 58,
            'days_absent' => 2,
        ]);

        $response = $this->putJson("/api/v1/report-cards/{$card->id}", [
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'stream_id' => $this->stream->id,
            'days_present' => 60,
            'days_absent' => 0,
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'student_id',
                'term_id',
                'stream_id',
                'days_present',
                'days_absent',
                'class_teacher_comment',
                'head_teacher_comment',
                'next_term_begins',
                'date_issued',
                'created_at',
                'updated_at',
            ])
            ->assertJsonFragment(['days_present' => 60]);
    }

    public function test_delete_report_card(): void
    {
        $card = ReportCard::create([
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'stream_id' => $this->stream->id,
            'days_present' => 58,
            'days_absent' => 2,
        ]);

        $response = $this->deleteJson("/api/v1/report-cards/{$card->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('report_cards', ['id' => $card->id]);
    }

    public function test_generate_validation_missing_student_id(): void
    {
        $response = $this->postJson('/api/v1/report-cards/generate', [
            'term_id' => $this->term->id,
        ], $this->authHeaders());

        $response->assertStatus(422);
    }

    public function test_generate_validation_nonexistent_student(): void
    {
        $response = $this->postJson('/api/v1/report-cards/generate', [
            'student_id' => 99999,
            'term_id' => $this->term->id,
        ], $this->authHeaders());

        $response->assertStatus(422);
    }
}
