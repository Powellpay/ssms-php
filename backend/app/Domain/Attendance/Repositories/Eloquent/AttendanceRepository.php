<?php

namespace App\Domain\Attendance\Repositories\Eloquent;

use App\Domain\Attendance\Models\Attendance;
use App\Domain\Attendance\Repositories\Contracts\AttendanceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AttendanceRepository implements AttendanceRepositoryInterface
{
    public function all(): Collection
    {
        return Attendance::all();
    }

    public function find(int $id): ?Attendance
    {
        return Attendance::find($id);
    }

    public function create(array $data): Attendance
    {
        return Attendance::create($data);
    }

    public function update(int $id, array $data): Attendance
    {
        $attendance = $this->find($id);
        $attendance->update($data);
        return $attendance;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)?->delete() ?? false;
    }

    public function findByStudentAndTerm(int $studentId, int $termId): Collection
    {
        return Attendance::where('student_id', $studentId)
            ->where('term_id', $termId)
            ->get();
    }

    public function findByDate(string $date): Collection
    {
        return Attendance::where('attendance_date', $date)->get();
    }

    public function attendanceSummary(int $studentId, int $termId): array
    {
        $records = Attendance::where('student_id', $studentId)
            ->where('term_id', $termId)
            ->get();

        $total = $records->count();
        $present = $records->where('status', 'present')->count();
        $absent = $records->where('status', 'absent')->count();
        $late = $records->where('status', 'late')->count();
        $excused = $records->where('status', 'excused')->count();

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'excused' => $excused,
        ];
    }
}
