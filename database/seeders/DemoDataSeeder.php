<?php

namespace Database\Seeders;

use App\Domain\Academic\Models\AcademicYear;
use App\Domain\Academic\Models\ClassLevel;
use App\Domain\Academic\Models\Stream;
use App\Domain\Academic\Models\Term;
use App\Domain\Assessment\Models\SubjectTermResult;
use App\Domain\Attendance\Models\Attendance;
use App\Domain\Curriculum\Models\Subject;
use App\Domain\Finance\Models\Invoice;
use App\Domain\Reports\Models\ReportCard;
use App\Domain\Staff\Models\Staff;
use App\Domain\Students\Models\Enrollment;
use App\Domain\Students\Models\Guardian;
use App\Domain\Students\Models\Student;
use App\Domain\Students\Models\StudentGuardian;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding demo data...');

        $faker = \Faker\Factory::create('en_UG');
        $yearId = 1;
        $termId = 1;

        // 1. Staff (7 more)
        $staffData = [
            ['STF-0004', 'Sarah', 'Nakato', 'Female', 'Senior Teacher', '2022-06-01'],
            ['STF-0005', 'Peter', 'Kato', 'Male', 'Teacher', '2023-09-15'],
            ['STF-0006', 'Esther', 'Muhindo', 'Female', 'Bursar', '2021-01-20'],
            ['STF-0007', 'John', 'Ochieng', 'Male', 'Teacher', '2024-02-10'],
            ['STF-0008', 'Mariam', 'Nabbanja', 'Female', 'Librarian', '2023-05-12'],
            ['STF-0009', 'Richard', 'Ssempijja', 'Male', 'Teacher', '2022-11-01'],
            ['STF-0010', 'Alice', 'Kyomugisha', 'Female', 'Teacher', '2024-03-01'],
        ];

        Staff::insert(array_map(fn($s) => [
            'staff_no' => $s[0],
            'first_name' => $s[1],
            'last_name' => $s[2],
            'gender' => $s[3],
            'designation' => $s[4],
            'date_joined' => $s[5],
            'status' => 'active',
        ], $staffData));

        $this->command->info('  ✅ 7 staff seeded');

        // 2. Students (15 more) — mix of gender, statuses
        $studentData = [
            ['S26-0003', 'James', 'Okot', 'Male', '2012-01-15', 'active'],
            ['S26-0004', 'Rebecca', 'Nalwoga', 'Female', '2012-04-20', 'active'],
            ['S26-0005', 'Emmanuel', 'Tumusiime', 'Male', '2013-08-10', 'active'],
            ['S26-0006', 'Grace', 'Akello', 'Female', '2011-11-05', 'active'],
            ['S26-0007', 'Patrick', 'Mwanga', 'Male', '2012-02-28', 'active'],
            ['S26-0008', 'Sophia', 'Nabunya', 'Female', '2012-06-14', 'active'],
            ['S26-0009', 'Ivan', 'Ssebunya', 'Male', '2011-09-30', 'active'],
            ['S26-0010', 'Doreen', 'Achola', 'Female', '2013-03-22', 'active'],
            ['S26-0011', 'Hassan', 'Mukasa', 'Male', '2012-12-01', 'transferred'],
            ['S26-0012', 'Martha', 'Nakawunde', 'Female', '2011-05-18', 'active'],
            ['S26-0013', 'Simon', 'Lule', 'Male', '2012-10-09', 'active'],
            ['S26-0014', 'Phiona', 'Babirye', 'Female', '2013-01-25', 'active'],
            ['S26-0015', 'David', 'Ouma', 'Male', '2010-07-12', 'graduated'],
            ['S26-0016', 'Sauda', 'Namatovu', 'Female', '2012-08-08', 'dropped'],
            ['S26-0017', 'Isaac', 'Kintu', 'Male', '2011-04-17', 'active'],
        ];

        Student::insert(array_map(fn($s) => [
            'admission_no' => $s[0],
            'first_name' => $s[1],
            'last_name' => $s[2],
            'gender' => $s[3],
            'dob' => $s[4],
            'admission_date' => '2026-02-03',
            'status' => $s[5],
        ], $studentData));

        $this->command->info('  ✅ 15 students seeded');

        // 3. Enrollments (one per new student, stream A or B)
        $streamIds = [1, 2, 3, 4, 5];
        $enrollments = [];
        for ($i = 3; $i <= 17; $i++) {
            $enrollments[] = [
                'student_id' => $i,
                'stream_id' => $streamIds[($i - 3) % 5],
                'academic_year_id' => $yearId,
                'enrollment_date' => '2026-02-03',
                'status' => ($i === 11) ? 'transferred' : (($i === 15) ? 'graduated' : (($i === 16) ? 'dropped' : 'active')),
            ];
        }
        Enrollment::insert($enrollments);

        $this->command->info('  ✅ 15 enrollments seeded');

        // 4. Guardians (5 shared)
        $guardianRecords = [
            ['Michael', 'Okot', 'Father', '+256772000001'],
            ['Sarah', 'Nalwoga', 'Mother', '+256772000002'],
            ['Robert', 'Tumusiime', 'Father', '+256772000003'],
            ['Joseph', 'Akello', 'Father', '+256772000004'],
            ['Agnes', 'Mwanga', 'Mother', '+256772000005'],
        ];
        Guardian::insert(array_map(fn($g) => [
            'first_name' => $g[0],
            'last_name' => $g[1],
            'relationship' => $g[2],
            'phone' => $g[3],
        ], $guardianRecords));

        // Link guardians to students
        $guardianLinks = [
            [3, 1], [4, 2], [5, 3], [6, 4], [7, 5],
            [8, 1], [9, 2], [10, 3], [11, 4], [12, 5],
            [13, 1], [14, 2], [15, 3], [16, 4], [17, 5],
        ];
        StudentGuardian::insert(array_map(fn($link) => [
            'student_id' => $link[0],
            'guardian_id' => $link[1],
            'is_primary_contact' => $link[1] === 1 ? true : false,
        ], $guardianLinks));

        $this->command->info('  ✅ 5 guardians + 15 links seeded');

        // 5. Attendance (15 records across students)
        $attendanceRecords = [];
        $statuses = ['Present', 'Present', 'Present', 'Present', 'Late', 'Present', 'Absent', 'Present', 'Present', 'Excused'];
        for ($i = 1; $i <= 15; $i++) {
            $attendanceRecords[] = [
                'student_id' => $i,
                'term_id' => $termId,
                'attendance_date' => "2026-02-0" . str_pad(min($i + 3, 28), 2, '0', STR_PAD_LEFT),
                'status' => $statuses[($i - 1) % 10],
            ];
        }
        Attendance::insert($attendanceRecords);

        $this->command->info('  ✅ 15 attendance records seeded');

        // 6. Subject Term Results for Daniel (student 2) — similar to Faith
        $danielResults = [
            ['subject_id' => 1, 'ca_score' => 13.00, 'eot_score' => 54.00, 'final_score' => 67.00, 'final_grade' => 'C', 'subject_teacher_comment' => 'Needs to improve reading comprehension.'],
            ['subject_id' => 2, 'ca_score' => 15.00, 'eot_score' => 50.00, 'final_score' => 65.00, 'final_grade' => 'C', 'subject_teacher_comment' => 'Good effort, needs practice with word problems.'],
            ['subject_id' => 3, 'ca_score' => 11.00, 'eot_score' => 42.00, 'final_score' => 53.00, 'final_grade' => 'D', 'subject_teacher_comment' => 'Struggles with classification; needs revision.'],
            ['subject_id' => 6, 'ca_score' => 14.00, 'eot_score' => 48.00, 'final_score' => 62.00, 'final_grade' => 'C', 'subject_teacher_comment' => 'Can improve on map interpretation.'],
            ['subject_id' => 9, 'ca_score' => 12.00, 'eot_score' => 40.00, 'final_score' => 52.00, 'final_grade' => 'D', 'subject_teacher_comment' => 'Should practice typing and file management.'],
            ['subject_id' => 11, 'ca_score' => 16.00, 'eot_score' => 60.00, 'final_score' => 76.00, 'final_grade' => 'B', 'subject_teacher_comment' => 'Shows keen interest in business concepts.'],
        ];

        foreach ($danielResults as $result) {
            SubjectTermResult::create(array_merge($result, ['student_id' => 2, 'term_id' => $termId]));
        }

        $this->command->info('  ✅ 6 subject term results seeded for Daniel');

        // 7. Invoices (3)
        Invoice::insert([
            ['student_id' => 1, 'term_id' => $termId, 'total_amount' => 450000.00, 'amount_paid' => 450000.00, 'issue_date' => '2026-02-03', 'status' => 'paid'],
            ['student_id' => 2, 'term_id' => $termId, 'total_amount' => 450000.00, 'amount_paid' => 200000.00, 'issue_date' => '2026-02-03', 'status' => 'partial'],
            ['student_id' => 3, 'term_id' => $termId, 'total_amount' => 450000.00, 'amount_paid' => 0.00, 'issue_date' => '2026-02-03', 'status' => 'unpaid'],
        ]);

        $this->command->info('  ✅ 3 invoices seeded');

        // 8. Report Cards for Daniel (student 2)
        ReportCard::create([
            'student_id' => 2,
            'term_id' => $termId,
            'stream_id' => 1,
            'days_present' => 55,
            'days_absent' => 5,
            'class_teacher_comment' => 'Daniel is a polite and respectful learner. He should focus more on Science subjects and ICT.',
            'head_teacher_comment' => 'A satisfactory first term. Aim higher next term, Daniel.',
            'next_term_begins' => '2026-05-25',
            'date_issued' => '2026-05-08',
        ]);

        $this->command->info('  ✅ 1 report card seeded');

        $total = 7 + 15 + 15 + 5 + 15 + 15 + 6 + 3 + 1;
        $this->command->info("  🎉 Demo data seeding complete! Total records created: {$total}");
    }
}