<?php

namespace App\Domain\Reports\Services;

use App\Domain\Assessment\Models\SubjectTermResult;
use App\Domain\Assessment\Models\GenericSkillRating;
use App\Domain\Attendance\Models\Attendance;
use App\Domain\Reports\Repositories\Contracts\ReportCardRepositoryInterface;
use App\Domain\Assessment\Repositories\Contracts\SubjectTermResultRepositoryInterface;
use App\Domain\Assessment\Repositories\Contracts\GenericSkillRatingRepositoryInterface;
use App\Domain\Attendance\Repositories\Contracts\AttendanceRepositoryInterface;
use App\Domain\Reports\Services\Contracts\ReportCardServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class ReportCardService implements ReportCardServiceInterface
{
    public function __construct(
        protected ReportCardRepositoryInterface $reportCardRepository,
        protected SubjectTermResultRepositoryInterface $subjectTermResultRepository,
        protected GenericSkillRatingRepositoryInterface $genericSkillRatingRepository,
        protected AttendanceRepositoryInterface $attendanceRepository
    ) {}

    public function all(): Collection
    {
        return $this->reportCardRepository->all();
    }

    public function find(int $id): ?\App\Domain\Reports\Models\ReportCard
    {
        return $this->reportCardRepository->find($id);
    }

    public function create(array $data): \App\Domain\Reports\Models\ReportCard
    {
        return $this->reportCardRepository->create($data);
    }

    public function update(int $id, array $data): \App\Domain\Reports\Models\ReportCard
    {
        return $this->reportCardRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->reportCardRepository->delete($id);
    }

    public function generateReportCard(int $studentId, int $termId): array
    {
        $subjectResults = SubjectTermResult::with('subject')
            ->where('student_id', $studentId)
            ->where('term_id', $termId)
            ->get();

        $skillRatings = GenericSkillRating::with('genericSkill', 'rating')
            ->where('student_id', $studentId)
            ->where('term_id', $termId)
            ->get();

        $attendanceSummary = [
            'present' => Attendance::where('student_id', $studentId)->where('term_id', $termId)->where('status', 'present')->count(),
            'absent' => Attendance::where('student_id', $studentId)->where('term_id', $termId)->where('status', 'absent')->count(),
            'late' => Attendance::where('student_id', $studentId)->where('term_id', $termId)->where('status', 'late')->count(),
            'excused' => Attendance::where('student_id', $studentId)->where('term_id', $termId)->where('status', 'excused')->count(),
        ];

        return [
            'student_id' => $studentId,
            'term_id' => $termId,
            'subject_results' => $subjectResults,
            'skill_ratings' => $skillRatings,
            'attendance' => $attendanceSummary,
        ];
    }
}
