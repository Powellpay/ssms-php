<?php

namespace App\Domain\Students\Jobs;

use App\Domain\Students\Models\Student;
use App\Domain\Students\Models\Enrollment;
use App\Domain\Students\Repositories\Contracts\StudentRepositoryInterface;
use App\Domain\Students\Services\Contracts\StudentServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

class ImportStudentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;

    public function __construct(
        protected array $rows,
        protected array $header,
        protected int $schoolId,
        protected ?int $defaultStreamId,
        protected ?int $academicYearId
    ) {}

    public function handle(StudentRepositoryInterface $studentRepo): void
    {
        $imported = 0;
        $errors = [];

        foreach ($this->rows as $i => $row) {
            $data = array_combine($this->header, $row);
            $rowNum = $i + 2;

            if (empty(trim($data['first_name'] ?? '')) || empty(trim($data['last_name'] ?? ''))) {
                $errors[] = "Row {$rowNum}: first_name and last_name are required.";
                continue;
            }

            $gender = strtolower(trim($data['gender'] ?? ''));
            if (!in_array($gender, ['male', 'female'])) {
                $errors[] = "Row {$rowNum}: gender must be Male or Female.";
                continue;
            }

            $admissionNo = trim($data['admission_no'] ?? ('STD-' . str_pad((string) (Student::max('id') + 1 + $imported), 4, '0', STR_PAD_LEFT)));
            $dob = !empty(trim($data['dob'] ?? '')) ? trim($data['dob']) : null;
            $admissionDate = !empty(trim($data['admission_date'] ?? '')) ? trim($data['admission_date']) : now()->toDateString();
            $status = in_array(strtolower(trim($data['status'] ?? 'active')), ['active', 'transferred', 'graduated', 'dropped'])
                ? strtolower(trim($data['status']))
                : 'active';

            try {
                DB::beginTransaction();

                $student = Student::create([
                    'school_id' => $this->schoolId,
                    'admission_no' => $admissionNo,
                    'first_name' => trim($data['first_name']),
                    'last_name' => trim($data['last_name']),
                    'gender' => ucfirst($gender),
                    'dob' => $dob,
                    'admission_date' => $admissionDate,
                    'status' => $status,
                ]);

                if ($this->defaultStreamId && $this->academicYearId) {
                    Enrollment::create([
                        'student_id' => $student->id,
                        'stream_id' => $this->defaultStreamId,
                        'academic_year_id' => $this->academicYearId,
                        'enrollment_date' => $admissionDate,
                        'status' => 'active',
                    ]);
                }

                DB::commit();
                $imported++;
            } catch (Throwable $e) {
                DB::rollBack();
                $errors[] = "Row {$rowNum}: {$e->getMessage()}";
            }
        }

        if (!empty($errors)) {
            \Log::warning('ImportStudentsJob: ' . count($errors) . ' errors, ' . $imported . ' imported', ['errors' => $errors]);
        }
    }
}
