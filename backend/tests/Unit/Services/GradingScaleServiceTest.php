<?php

namespace Tests\Unit\Services;

use App\Models\GradingScale;
use App\Repositories\Contracts\GradingScaleRepositoryInterface;
use App\Services\GradingScaleService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class GradingScaleServiceTest extends TestCase
{
    private MockInterface $gradingScaleRepository;
    private GradingScaleService $gradingScaleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gradingScaleRepository = Mockery::mock(GradingScaleRepositoryInterface::class);
        $this->gradingScaleService = new GradingScaleService($this->gradingScaleRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_getGrade_returns_A_for_scores_80_to_100(): void
    {
        $grade = GradingScale::make(['grade' => 'A', 'min_score' => 80, 'max_score' => 100]);

        $this->gradingScaleRepository->shouldReceive('findScoreGrade')
            ->with(85.0)
            ->once()
            ->andReturn($grade);

        $result = $this->gradingScaleService->getGrade(85.0);

        $this->assertSame($grade, $result);
    }

    public function test_getGrade_returns_C_for_mid_range_score(): void
    {
        $grade = GradingScale::make(['grade' => 'C', 'min_score' => 55, 'max_score' => 69.99]);

        $this->gradingScaleRepository->shouldReceive('findScoreGrade')
            ->with(62.0)
            ->once()
            ->andReturn($grade);

        $result = $this->gradingScaleService->getGrade(62.0);

        $this->assertSame($grade, $result);
    }

    public function test_getGrade_returns_E_for_low_score(): void
    {
        $grade = GradingScale::make(['grade' => 'E', 'min_score' => 0, 'max_score' => 39.99]);

        $this->gradingScaleRepository->shouldReceive('findScoreGrade')
            ->with(25.0)
            ->once()
            ->andReturn($grade);

        $result = $this->gradingScaleService->getGrade(25.0);

        $this->assertSame($grade, $result);
    }

    public function test_getGrade_returns_null_for_score_outside_range(): void
    {
        $this->gradingScaleRepository->shouldReceive('findScoreGrade')
            ->with(101.0)
            ->once()
            ->andReturn(null);

        $result = $this->gradingScaleService->getGrade(101.0);

        $this->assertNull($result);
    }

    public function test_all_returns_all_grades(): void
    {
        $grades = new Collection([
            GradingScale::make(['grade' => 'A']),
            GradingScale::make(['grade' => 'B']),
            GradingScale::make(['grade' => 'C']),
        ]);

        $this->gradingScaleRepository->shouldReceive('all')->once()->andReturn($grades);

        $result = $this->gradingScaleService->all();

        $this->assertCount(3, $result);
    }

    public function test_create_delegates_to_repository(): void
    {
        $data = ['grade' => 'A', 'descriptor' => 'Exceptional', 'min_score' => 80, 'max_score' => 100];
        $grade = GradingScale::make($data);

        $this->gradingScaleRepository->shouldReceive('create')->with($data)->once()->andReturn($grade);

        $result = $this->gradingScaleService->create($data);

        $this->assertSame($grade, $result);
    }
}
