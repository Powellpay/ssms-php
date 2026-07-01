<?php

namespace Tests\Unit\Services;

use App\Models\AcademicYear;
use App\Repositories\Contracts\AcademicYearRepositoryInterface;
use App\Services\AcademicYearService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class AcademicYearServiceTest extends TestCase
{
    private MockInterface $academicYearRepository;
    private AcademicYearService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->academicYearRepository = Mockery::mock(AcademicYearRepositoryInterface::class);
        $this->service = new AcademicYearService($this->academicYearRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_setCurrent_updates_only_target_year(): void
    {
        $year1 = new AcademicYear(['is_current' => true]);
        $year1->id = 1;
        $year2 = new AcademicYear(['is_current' => false]);
        $year2->id = 2;

        $this->academicYearRepository->shouldReceive('findCurrent')
            ->once()
            ->andReturn($year1);

        $this->academicYearRepository->shouldReceive('update')
            ->with(1, ['is_current' => false])
            ->once()
            ->andReturn($year1);

        $this->academicYearRepository->shouldReceive('update')
            ->with(2, ['is_current' => true])
            ->once()
            ->andReturn($year2);

        $this->service->setCurrent(2);

        $this->assertTrue(true);
    }

    public function test_findCurrent_delegates_to_repository(): void
    {
        $current = AcademicYear::make(['id' => 1, 'year_name' => '2026', 'is_current' => true]);

        $this->academicYearRepository->shouldReceive('findCurrent')->once()->andReturn($current);

        $result = $this->service->findCurrent();

        $this->assertSame($current, $result);
    }

    public function test_findCurrent_returns_null_when_none_set(): void
    {
        $this->academicYearRepository->shouldReceive('findCurrent')->once()->andReturn(null);

        $result = $this->service->findCurrent();

        $this->assertNull($result);
    }
}
