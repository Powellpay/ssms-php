<?php

namespace App\Domain\Students\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Students\Models\Student;
use App\Domain\Students\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentImportController extends Controller
{
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
            'stream_id' => 'nullable|integer|exists:streams,id',
            'academic_year_id' => 'nullable|integer|exists:academic_years,id',
        ]);

        $file = $request->file('file');
        $schoolId = (int) $request->user()->school_id;
        $defaultStreamId = $request->input('stream_id') ? (int) $request->input('stream_id') : null;
        $academicYearId = $request->input('academic_year_id') ? (int) $request->input('academic_year_id') : null;

        $csv = array_map('str_getcsv', file($file->getRealPath()));
        if (empty($csv) || count($csv) < 2) {
            return response()->json(['success' => false, 'message' => 'CSV must have header + at least one row.'], 422);
        }

        $header = array_map('trim', $csv[0]);
        $expected = ['first_name', 'last_name', 'gender'];
        $missing = array_diff($expected, $header);
        if (!empty($missing)) {
            return response()->json(['success' => false, 'message' => 'Missing columns: ' . implode(', ', $missing)], 422);
        }

        $rows = array_slice($csv, 1);
        $totalRows = count($rows);
        $imported = 0;
        $errors = [];

        foreach (array_chunk($rows, 100) as $chunkIndex => $chunk) {
            foreach ($chunk as $i => $row) {
                $data = array_combine($header, $row);
                $rowNum = ($chunkIndex * 100) + $i + 2;

                if (empty(trim($data['first_name'] ?? '')) || empty(trim($data['last_name'] ?? ''))) {
                    $errors[] = "Row {$rowNum}: first_name and last_name required.";
                    continue;
                }

                $gender = strtolower(trim($data['gender'] ?? ''));
                if (!in_array($gender, ['male', 'female'])) {
                    $errors[] = "Row {$rowNum}: gender must be Male or Female.";
                    continue;
                }

                try {
                    DB::beginTransaction();

                    $admissionNo = trim($data['admission_no'] ?? ('STD-' . str_pad((string) (Student::max('id') + 1 + $imported), 4, '0', STR_PAD_LEFT)));

                    $student = Student::create([
                        'school_id' => $schoolId,
                        'admission_no' => $admissionNo,
                        'first_name' => trim($data['first_name']),
                        'last_name' => trim($data['last_name']),
                        'gender' => ucfirst($gender),
                        'dob' => !empty(trim($data['dob'] ?? '')) ? trim($data['dob']) : null,
                        'admission_date' => !empty(trim($data['admission_date'] ?? '')) ? trim($data['admission_date']) : now()->toDateString(),
                        'status' => in_array(strtolower(trim($data['status'] ?? 'active')), ['active', 'transferred', 'graduated', 'dropped'])
                            ? strtolower(trim($data['status'])) : 'active',
                    ]);

                    if ($defaultStreamId && $academicYearId) {
                        Enrollment::create([
                            'student_id' => $student->id,
                            'stream_id' => $defaultStreamId,
                            'academic_year_id' => $academicYearId,
                            'enrollment_date' => $student->admission_date,
                            'status' => 'active',
                        ]);
                    }

                    DB::commit();
                    $imported++;
                } catch (\Throwable $e) {
                    DB::rollBack();
                    $errors[] = "Row {$rowNum}: {$e->getMessage()}";
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Imported {$imported} of {$totalRows} student(s)." . (count($errors) ? " " . count($errors) . " error(s)." : ''),
            'imported' => $imported,
            'total' => $totalRows,
            'errors' => $errors,
        ]);
    }

    public function downloadTemplate()
    {
        $headers = ['first_name', 'last_name', 'gender', 'admission_no', 'dob', 'admission_date', 'status'];
        $sample = ['Jane', 'Doe', 'Female', 'STD-2026-0001', '2012-01-15', '2026-02-03', 'active'];

        $callback = function () use ($headers, $sample) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            fputcsv($handle, $sample);
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="student-import-template.csv"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }
}
