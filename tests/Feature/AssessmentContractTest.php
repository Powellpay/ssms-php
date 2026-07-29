<?php

namespace Tests\Feature;

use App\Domain\Academic\Models\AcademicYear;
use App\Domain\Academic\Models\Term;
use App\Domain\Assessment\Models\AssessmentRecord;
use App\Domain\Assessment\Models\AssessmentType;
use App\Domain\Assessment\Models\GenericSkillRating;
use App\Domain\Assessment\Models\GradingScale;
use App\Domain\Assessment\Models\SkillRatingScale;
use App\Domain\Assessment\Models\SubjectTermResult;
use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Models\User;
use App\Domain\Curriculum\Models\GenericSkill;
use App\Domain\Curriculum\Models\Subject;
use App\Domain\Staff\Models\Staff;
use App\Domain\Students\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentContractTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private Student $student;
    private Subject $subject;
    private AssessmentType $assessmentType;
    private Term $term;
    private Staff $staff;
    private AcademicYear $academicYear;
    private GenericSkill $genericSkill;
    private SkillRatingScale $rating;

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
            'year_name' => '2026', 'start_date' => '2026-02-03', 'end_date' => '2026-12-04',
        ]);
        $this->term = Term::create([
            'academic_year_id' => $this->academicYear->id, 'term_name' => 'Term 1',
            'start_date' => '2026-02-03', 'end_date' => '2026-05-08',
        ]);
        $this->student = Student::create([
            'admission_no' => 'S26-0001', 'first_name' => 'Faith', 'last_name' => 'Achieng',
            'gender' => 'Female', 'admission_date' => '2026-02-03',
        ]);
        $this->subject = Subject::create([
            'subject_code' => 'MTC', 'subject_name' => 'Mathematics', 'category' => 'Core',
        ]);
        $this->assessmentType = AssessmentType::create([
            'type_name' => 'Continuous Assessment Test', 'category' => 'Formative',
        ]);
        $this->staff = Staff::create([
            'staff_no' => 'STF-001', 'first_name' => 'Jane', 'last_name' => 'Doe', 'gender' => 'Female', 'status' => 'active',
        ]);
        $this->genericSkill = GenericSkill::create(['skill_name' => 'Communication']);
        $this->rating = SkillRatingScale::create([
            'rating_code' => 'BEG', 'rating_label' => 'Beginning', 'rating_value' => 1,
        ]);
    }

    private function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_list_assessment_types_structure(): void
    {
        $response = $this->getJson('/api/v1/assessment-types', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['*' => ['id', 'type_name', 'category', 'weight_percentage', 'description', 'created_at', 'updated_at']]]);
    }

    public function test_create_assessment_type(): void
    {
        $response = $this->postJson('/api/v1/assessment-types', [
            'type_name' => 'End of Term Exam', 'category' => 'Summative',
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['type_name' => 'End of Term Exam']);
    }

    public function test_update_assessment_type(): void
    {
        $response = $this->putJson("/api/v1/assessment-types/{$this->assessmentType->id}", [
            'type_name' => 'CAT 2', 'category' => 'Formative',
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['type_name' => 'CAT 2']);
    }

    public function test_delete_assessment_type(): void
    {
        $response = $this->deleteJson("/api/v1/assessment-types/{$this->assessmentType->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('assessment_types', ['id' => $this->assessmentType->id]);
    }

    public function test_assessment_type_validation(): void
    {
        $response = $this->postJson('/api/v1/assessment-types', [], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type_name', 'category']);
    }

    public function test_list_grading_scales_structure(): void
    {
        $response = $this->getJson('/api/v1/grading-scale', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['*' => ['id', 'grade', 'descriptor', 'min_score', 'max_score', 'remarks', 'created_at', 'updated_at']]]);
    }

    public function test_create_grading_scale(): void
    {
        $response = $this->postJson('/api/v1/grading-scale', [
            'grade' => 'A', 'descriptor' => 'Exceptional', 'min_score' => 80, 'max_score' => 100,
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['grade' => 'A']);
    }

    public function test_update_grading_scale(): void
    {
        $gs = GradingScale::create(['grade' => 'B', 'descriptor' => 'Good', 'min_score' => 70, 'max_score' => 79.99]);

        $response = $this->putJson("/api/v1/grading-scale/{$gs->id}", [
            'grade' => 'B', 'descriptor' => 'Very Good', 'min_score' => 70, 'max_score' => 79.99,
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['descriptor' => 'Very Good']);
    }

    public function test_delete_grading_scale(): void
    {
        $gs = GradingScale::create(['grade' => 'E', 'descriptor' => 'Elementary', 'min_score' => 0, 'max_score' => 39.99]);

        $response = $this->deleteJson("/api/v1/grading-scale/{$gs->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('grading_scale', ['id' => $gs->id]);
    }

    public function test_grading_scale_validation(): void
    {
        $response = $this->postJson('/api/v1/grading-scale', [], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['grade', 'descriptor', 'min_score', 'max_score']);
    }

    public function test_grading_scale_score_range(): void
    {
        $response = $this->postJson('/api/v1/grading-scale', [
            'grade' => 'A', 'descriptor' => 'Invalid', 'min_score' => 90, 'max_score' => 80,
        ], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['max_score']);
    }

    public function test_list_skill_rating_scales_structure(): void
    {
        $response = $this->getJson('/api/v1/skill-rating-scale', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['*' => ['id', 'rating_code', 'rating_label', 'rating_value', 'description', 'created_at', 'updated_at']]]);
    }

    public function test_create_skill_rating_scale(): void
    {
        $response = $this->postJson('/api/v1/skill-rating-scale', [
            'rating_code' => 'DEV', 'rating_label' => 'Developing', 'rating_value' => 2,
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['rating_code' => 'DEV']);
    }

    public function test_update_skill_rating_scale(): void
    {
        $response = $this->putJson("/api/v1/skill-rating-scale/{$this->rating->id}", [
            'rating_code' => 'BEG', 'rating_label' => 'Beginner', 'rating_value' => 1,
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['rating_label' => 'Beginner']);
    }

    public function test_delete_skill_rating_scale(): void
    {
        $response = $this->deleteJson("/api/v1/skill-rating-scale/{$this->rating->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('skill_rating_scale', ['id' => $this->rating->id]);
    }

    public function test_skill_rating_scale_validation(): void
    {
        $response = $this->postJson('/api/v1/skill-rating-scale', [], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['rating_code', 'rating_label', 'rating_value']);
    }

    public function test_list_assessment_records_structure(): void
    {
        $response = $this->getJson('/api/v1/assessment-records', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['*' => ['id', 'student_id', 'subject_id', 'theme_id', 'learning_outcome_id', 'assessment_type_id', 'term_id', 'score', 'max_score', 'grade', 'remarks', 'date_recorded', 'recorded_by', 'created_at', 'updated_at']]]);
    }

    public function test_create_assessment_record(): void
    {
        $response = $this->postJson('/api/v1/assessment-records', [
            'student_id' => $this->student->id, 'subject_id' => $this->subject->id,
            'assessment_type_id' => $this->assessmentType->id, 'term_id' => $this->term->id,
            'score' => 75, 'date_recorded' => '2026-03-15', 'recorded_by' => $this->staff->id,
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['student_id' => $this->student->id]);
    }

    public function test_update_assessment_record(): void
    {
        $record = AssessmentRecord::create([
            'student_id' => $this->student->id, 'subject_id' => $this->subject->id,
            'assessment_type_id' => $this->assessmentType->id, 'term_id' => $this->term->id,
            'score' => 50, 'date_recorded' => '2026-03-15', 'recorded_by' => $this->staff->id,
        ]);

        $response = $this->putJson("/api/v1/assessment-records/{$record->id}", [
            'student_id' => $this->student->id, 'subject_id' => $this->subject->id,
            'assessment_type_id' => $this->assessmentType->id, 'term_id' => $this->term->id,
            'score' => 85, 'date_recorded' => '2026-03-15', 'recorded_by' => $this->staff->id,
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['score' => '85.00']);
    }

    public function test_delete_assessment_record(): void
    {
        $record = AssessmentRecord::create([
            'student_id' => $this->student->id, 'subject_id' => $this->subject->id,
            'assessment_type_id' => $this->assessmentType->id, 'term_id' => $this->term->id,
            'score' => 60, 'date_recorded' => '2026-03-15', 'recorded_by' => $this->staff->id,
        ]);

        $response = $this->deleteJson("/api/v1/assessment-records/{$record->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('assessment_records', ['id' => $record->id]);
    }

    public function test_assessment_record_validation(): void
    {
        $response = $this->postJson('/api/v1/assessment-records', [], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['student_id', 'subject_id', 'assessment_type_id', 'term_id', 'date_recorded']);
    }

    public function test_list_generic_skill_ratings_structure(): void
    {
        $response = $this->getJson('/api/v1/generic-skill-ratings', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['*' => ['id', 'student_id', 'term_id', 'generic_skill_id', 'rating_id', 'remarks', 'recorded_by', 'created_at', 'updated_at']]]);
    }

    public function test_create_generic_skill_rating(): void
    {
        $response = $this->postJson('/api/v1/generic-skill-ratings', [
            'student_id' => $this->student->id, 'term_id' => $this->term->id,
            'generic_skill_id' => $this->genericSkill->id, 'rating_id' => $this->rating->id,
            'recorded_by' => $this->staff->id,
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['student_id' => $this->student->id]);
    }

    public function test_update_generic_skill_rating(): void
    {
        $gsr = GenericSkillRating::create([
            'student_id' => $this->student->id, 'term_id' => $this->term->id,
            'generic_skill_id' => $this->genericSkill->id, 'rating_id' => $this->rating->id,
            'recorded_by' => $this->staff->id,
        ]);

        $response = $this->putJson("/api/v1/generic-skill-ratings/{$gsr->id}", [
            'student_id' => $this->student->id, 'term_id' => $this->term->id,
            'generic_skill_id' => $this->genericSkill->id, 'rating_id' => $this->rating->id,
        ], $this->authHeaders());

        $response->assertStatus(200);
    }

    public function test_delete_generic_skill_rating(): void
    {
        $gsr = GenericSkillRating::create([
            'student_id' => $this->student->id, 'term_id' => $this->term->id,
            'generic_skill_id' => $this->genericSkill->id, 'rating_id' => $this->rating->id,
            'recorded_by' => $this->staff->id,
        ]);

        $response = $this->deleteJson("/api/v1/generic-skill-ratings/{$gsr->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('generic_skill_ratings', ['id' => $gsr->id]);
    }

    public function test_generic_skill_rating_validation(): void
    {
        $response = $this->postJson('/api/v1/generic-skill-ratings', [], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['student_id', 'term_id', 'generic_skill_id', 'rating_id']);
    }

    public function test_list_subject_term_results_structure(): void
    {
        $response = $this->getJson('/api/v1/subject-term-results', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['*' => ['id', 'student_id', 'subject_id', 'term_id', 'ca_score', 'eot_score', 'final_score', 'final_grade', 'subject_teacher_comment', 'created_at', 'updated_at']]]);
    }

    public function test_create_subject_term_result(): void
    {
        $response = $this->postJson('/api/v1/subject-term-results', [
            'student_id' => $this->student->id, 'subject_id' => $this->subject->id,
            'term_id' => $this->term->id, 'ca_score' => 16, 'eot_score' => 60,
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['student_id' => $this->student->id]);
    }

    public function test_update_subject_term_result(): void
    {
        $str = SubjectTermResult::create([
            'student_id' => $this->student->id, 'subject_id' => $this->subject->id,
            'term_id' => $this->term->id, 'ca_score' => 14, 'eot_score' => 50,
            'final_score' => 64, 'final_grade' => 'C',
        ]);

        $response = $this->putJson("/api/v1/subject-term-results/{$str->id}", [
            'student_id' => $this->student->id, 'subject_id' => $this->subject->id,
            'term_id' => $this->term->id, 'ca_score' => 18, 'eot_score' => 70,
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['ca_score' => '18.00']);
    }

    public function test_delete_subject_term_result(): void
    {
        $str = SubjectTermResult::create([
            'student_id' => $this->student->id, 'subject_id' => $this->subject->id,
            'term_id' => $this->term->id, 'ca_score' => 14, 'eot_score' => 50,
            'final_score' => 64, 'final_grade' => 'C',
        ]);

        $response = $this->deleteJson("/api/v1/subject-term-results/{$str->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('subject_term_results', ['id' => $str->id]);
    }

    public function test_subject_term_result_validation(): void
    {
        $response = $this->postJson('/api/v1/subject-term-results', [], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['student_id', 'subject_id', 'term_id']);
    }

    public function test_subject_term_result_scores(): void
    {
        $response = $this->postJson('/api/v1/subject-term-results', [
            'student_id' => $this->student->id, 'subject_id' => $this->subject->id,
            'term_id' => $this->term->id, 'ca_score' => 25, 'eot_score' => 85,
        ], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ca_score', 'eot_score']);
    }

    public function test_auth_required(): void
    {
        $this->getJson('/api/v1/assessment-types')->assertStatus(401);
        $this->getJson('/api/v1/grading-scale')->assertStatus(401);
        $this->getJson('/api/v1/skill-rating-scale')->assertStatus(401);
        $this->getJson('/api/v1/assessment-records')->assertStatus(401);
        $this->getJson('/api/v1/generic-skill-ratings')->assertStatus(401);
        $this->getJson('/api/v1/subject-term-results')->assertStatus(401);
    }
}
