<?php

namespace Tests\Unit\Services;

use App\Models\GradingScale;
use App\Models\SubjectTermResult;
use App\Repositories\Contracts\SubjectTermResultRepositoryInterface;
use App\Services\Contracts\GradingScaleServiceInterface;
use App\Services\SubjectTermResultService;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class SubjectTermResultServiceTest extends TestCase
{
    private MockInterface $subjectTermResultRepository;
    private MockInterface $gradingScaleService;
    private SubjectTermResultService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subjectTermResultRepository = Mockery::mock(SubjectTermResultRepositoryInterface::class);
        $this->gradingScaleService = Mockery::mock(GradingScaleServiceInterface::class);
        $this->service = new SubjectTermResultService(
            $this->subjectTermResultRepository,
            $this->gradingScaleService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_computeResult_creates_new_result_when_none_exists(): void
    {
        $grade = GradingScale::make(['grade' => 'B']);

        $this->gradingScaleService->shouldReceive('getGrade')->with(76.0)->once()->andReturn($grade);

        $this->subjectTermResultRepository->shouldReceive('findByStudentSubjectTerm')
            ->with(1, 2, 1)
            ->once()
            ->andReturn(null);

        $this->subjectTermResultRepository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return $data['student_id'] === 1
                    && $data['subject_id'] === 2
                    && $data['term_id'] === 1
                    && $data['ca_score'] === 16.0
                    && $data['eot_score'] === 60.0
                    && $data['final_score'] === 76.0
                    && $data['final_grade'] === 'B';
            }))
            ->andReturn(SubjectTermResult::make([
                'student_id' => 1,
                'subject_id' => 2,
                'term_id' => 1,
                'ca_score' => 16.0,
                'eot_score' => 60.0,
                'final_score' => 76.0,
                'final_grade' => 'B',
            ]));

        $result = $this->service->computeResult(1, 2, 1, 16.0, 60.0);

        $this->assertEquals(76.0, $result->final_score);
        $this->assertEquals('B', $result->final_grade);
    }

    public function test_computeResult_updates_existing_result(): void
    {
        $grade = GradingScale::make(['grade' => 'A']);
        $existing = new SubjectTermResult(['student_id' => 1, 'subject_id' => 2, 'term_id' => 1]);
        $existing->id = 5;

        $this->gradingScaleService->shouldReceive('getGrade')->with(90.0)->once()->andReturn($grade);

        $this->subjectTermResultRepository->shouldReceive('findByStudentSubjectTerm')
            ->with(1, 2, 1)
            ->once()
            ->andReturn($existing);

        $updated = SubjectTermResult::make([
            'id' => 5, 'student_id' => 1, 'subject_id' => 2, 'term_id' => 1,
            'ca_score' => 18.0, 'eot_score' => 72.0, 'final_score' => 90.0, 'final_grade' => 'A',
        ]);

        $this->subjectTermResultRepository->shouldReceive('update')
            ->with(5, Mockery::on(function ($data) {
                return $data['ca_score'] === 18.0 && $data['eot_score'] === 72.0
                    && $data['final_score'] === 90.0 && $data['final_grade'] === 'A';
            }))
            ->once()
            ->andReturn($updated);

        $result = $this->service->computeResult(1, 2, 1, 18.0, 72.0);

        $this->assertEquals(90.0, $result->final_score);
        $this->assertEquals('A', $result->final_grade);
    }

    public function test_computeResult_handles_missing_grade_gracefully(): void
    {
        $this->gradingScaleService->shouldReceive('getGrade')->with(150.0)->once()->andReturn(null);

        $this->subjectTermResultRepository->shouldReceive('findByStudentSubjectTerm')
            ->with(1, 2, 1)
            ->once()
            ->andReturn(null);

        $this->subjectTermResultRepository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return $data['final_score'] === 150.0 && $data['final_grade'] === null;
            }))
            ->andReturn(SubjectTermResult::make([
                'student_id' => 1, 'subject_id' => 2, 'term_id' => 1,
                'ca_score' => 70.0, 'eot_score' => 80.0,
                'final_score' => 150.0, 'final_grade' => null,
            ]));

        $result = $this->service->computeResult(1, 2, 1, 70.0, 80.0);

        $this->assertEquals(150.0, $result->final_score);
        $this->assertNull($result->final_grade);
    }
}
