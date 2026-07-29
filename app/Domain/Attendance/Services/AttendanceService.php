<?php

namespace App\Domain\Attendance\Services;

use App\Domain\Attendance\Models\Attendance;
use App\Domain\Attendance\Repositories\Contracts\AttendanceRepositoryInterface;
use App\Domain\Attendance\Services\Contracts\AttendanceServiceInterface;
use App\Domain\Students\Models\Enrollment;
use Illuminate\Database\Eloquent\Collection;

class AttendanceService implements AttendanceServiceInterface
{
    public function __construct(
        protected AttendanceRepositoryInterface $attendanceRepository
    ) {}

    public function all(): Collection
    {
        return $this->attendanceRepository->all();
    }

    public function find(int $id): ?Attendance
    {
        return $this->attendanceRepository->find($id);
    }

    public function create(array $data): Attendance
    {
        return $this->attendanceRepository->create($data);
    }

    public function update(int $id, array $data): Attendance
    {
        return $this->attendanceRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->attendanceRepository->delete($id);
    }

    public function markAttendance(int $studentId, int $termId, string $date, string $status): Attendance
    {
        return $this->attendanceRepository->create([
            'student_id' => $studentId,
            'term_id' => $termId,
            'attendance_date' => $date,
            'status' => $status,
        ]);
    }

    public function getAttendanceSummary(int $studentId, int $termId): array
    {
        return [
            'present' => Attendance::where('student_id', $studentId)->where('term_id', $termId)->where('status', 'present')->count(),
            'absent' => Attendance::where('student_id', $studentId)->where('term_id', $termId)->where('status', 'absent')->count(),
            'late' => Attendance::where('student_id', $studentId)->where('term_id', $termId)->where('status', 'late')->count(),
            'excused' => Attendance::where('student_id', $studentId)->where('term_id', $termId)->where('status', 'excused')->count(),
        ];
    }

    public function markBulkAttendance(array $records, int $termId, string $date, int $recordedBy): array
    {
        $count = 0;

        foreach ($records as $record) {
            Attendance::where('student_id', $record['student_id'])
                ->where('attendance_date', $date)
                ->delete();

            Attendance::create([
                'student_id' => $record['student_id'],
                'term_id' => $termId,
                'attendance_date' => $date,
                'status' => $record['status'],
                'recorded_by' => $recordedBy,
            ]);

            $count++;
        }

        return ['count' => $count];
    }

    public function getRegister(int $termId, string $date, ?int $streamId): array
    {
        if ($streamId) {
            $enrollments = Enrollment::where('stream_id', $streamId)
                ->where('status', 'active')
                ->with('student')
                ->get();
        } else {
            $enrollments = collect();
        }

        $register = [];

        foreach ($enrollments as $enrollment) {
            $student = $enrollment->student;
            $attendance = Attendance::where('student_id', $student->id)
                ->where('attendance_date', $date)
                ->first();

            $register[] = [
                'student_id' => $student->id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'admission_no' => $student->admission_no,
                'status' => $attendance?->status,
            ];
        }

        return $register;
    }
}
