<?php

namespace Database\Seeders;

use App\Domain\Academic\Models\AcademicYear;
use App\Domain\Assessment\Models\AssessmentType;
use App\Domain\Attendance\Models\Attendance;
use App\Domain\Academic\Models\ClassLevel;
use App\Domain\Curriculum\Models\CurriculumTheme;
use App\Domain\Students\Models\Enrollment;
use App\Domain\Curriculum\Models\GenericSkill;
use App\Domain\Assessment\Models\GenericSkillRating;
use App\Domain\Assessment\Models\GradingScale;
use App\Domain\Students\Models\Guardian;
use App\Domain\Curriculum\Models\LearningOutcome;
use App\Domain\Reports\Models\ReportCard;
use App\Domain\Auth\Models\Role;
use App\Domain\Assessment\Models\SkillRatingScale;
use App\Domain\Staff\Models\Staff;
use App\Domain\Academic\Models\Stream;
use App\Domain\Students\Models\Student;
use App\Domain\Students\Models\StudentGuardian;
use App\Domain\Curriculum\Models\Subject;
use App\Domain\Assessment\Models\SubjectTermResult;
use App\Domain\Academic\Models\Term;
use App\Domain\Auth\Models\User;
use App\Domain\Auth\Models\School;
use App\Domain\Auth\Services\ModuleAccessService;
use App\Domain\Curriculum\Models\ClassSubject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // School
        $school = School::create([
            'name' => 'Oscar Demonstration School',
            'email' => 'oscar@gmail.com',
            'phone' => '+256700000000',
            'address' => 'Kampala, Uganda',
            'motto' => 'Excellence in Education',
            'status' => 'active',
        ]);
        $sid = $school->id;

        // Roles (global defaults — available to all schools)
        $defaultRoles = [
            ['role_name' => 'Administrator', 'slug' => 'administrator', 'description' => 'Full system access'],
            ['role_name' => 'Head Teacher', 'slug' => 'head-teacher', 'description' => 'School head, approves report cards'],
            ['role_name' => 'Director of Studies', 'slug' => 'director-of-studies', 'description' => 'Oversees academics and curriculum'],
            ['role_name' => 'Teacher', 'slug' => 'teacher', 'description' => 'Subject/class teacher'],
            ['role_name' => 'Bursar', 'slug' => 'bursar', 'description' => 'Manages fees and finance'],
            ['role_name' => 'Librarian', 'slug' => 'librarian', 'description' => 'Manages library'],
            ['role_name' => 'Parent', 'slug' => 'parent', 'description' => 'Views child progress and fees'],
            ['role_name' => 'Student', 'slug' => 'student', 'description' => 'Views own results and timetable'],
        ];

        foreach ($defaultRoles as $roleData) {
            Role::updateOrCreate(
                ['slug' => $roleData['slug'], 'school_id' => null],
                $roleData
            );
        }

        User::create([
            'school_id' => $sid,
            'role_id' => 1,
            'username' => 'admin',
            'name' => 'Oscar Opiyo',
            'email' => 'oscar@gmail.com',
            'password' => Hash::make('ChangeMe123!'),
            'status' => 'active',
            'modules' => ModuleAccessService::ALL_MODULES,
        ]);

        // Academic Years
        AcademicYear::create([
            'school_id' => $sid,
            'year_name' => '2026',
            'start_date' => '2026-02-03',
            'end_date' => '2026-12-04',
            'is_current' => true,
        ]);

        // Terms
        Term::insert([
            ['school_id' => $sid, 'academic_year_id' => 1, 'term_name' => 'Term 1', 'start_date' => '2026-02-03', 'end_date' => '2026-05-08', 'next_term_begins' => '2026-05-25', 'is_current' => true],
            ['school_id' => $sid, 'academic_year_id' => 1, 'term_name' => 'Term 2', 'start_date' => '2026-05-25', 'end_date' => '2026-08-21', 'next_term_begins' => '2026-09-07', 'is_current' => false],
            ['school_id' => $sid, 'academic_year_id' => 1, 'term_name' => 'Term 3', 'start_date' => '2026-09-07', 'end_date' => '2026-12-04', 'next_term_begins' => null, 'is_current' => false],
        ]);

        // Class Levels
        ClassLevel::insert([
            ['school_id' => $sid, 'level_name' => 'S1', 'numeric_level' => 1, 'description' => 'Senior One'],
            ['school_id' => $sid, 'level_name' => 'S2', 'numeric_level' => 2, 'description' => 'Senior Two'],
            ['school_id' => $sid, 'level_name' => 'S3', 'numeric_level' => 3, 'description' => 'Senior Three'],
            ['school_id' => $sid, 'level_name' => 'S4', 'numeric_level' => 4, 'description' => 'Senior Four'],
        ]);

        // Staff
        Staff::insert([
            ['school_id' => $sid, 'staff_no' => 'STF-0001', 'first_name' => 'Brian', 'last_name' => 'Mutebi', 'gender' => 'Male', 'designation' => 'Director of Studies', 'date_joined' => '2022-01-10', 'status' => 'active'],
            ['school_id' => $sid, 'staff_no' => 'STF-0002', 'first_name' => 'Grace', 'last_name' => 'Namutebi', 'gender' => 'Female', 'designation' => 'Teacher', 'date_joined' => '2023-02-01', 'status' => 'active'],
            ['school_id' => $sid, 'staff_no' => 'STF-0003', 'first_name' => 'Samuel', 'last_name' => 'Okello', 'gender' => 'Male', 'designation' => 'Teacher', 'date_joined' => '2021-08-15', 'status' => 'active'],
        ]);

        // Streams
        Stream::insert([
            ['school_id' => $sid, 'class_level_id' => 1, 'academic_year_id' => 1, 'stream_name' => 'A', 'class_teacher_id' => 2],
            ['school_id' => $sid, 'class_level_id' => 1, 'academic_year_id' => 1, 'stream_name' => 'B', 'class_teacher_id' => 3],
            ['school_id' => $sid, 'class_level_id' => 2, 'academic_year_id' => 1, 'stream_name' => 'A', 'class_teacher_id' => null],
            ['school_id' => $sid, 'class_level_id' => 3, 'academic_year_id' => 1, 'stream_name' => 'A', 'class_teacher_id' => null],
            ['school_id' => $sid, 'class_level_id' => 4, 'academic_year_id' => 1, 'stream_name' => 'A', 'class_teacher_id' => null],
        ]);

        // Subjects
        Subject::insert([
            ['school_id' => $sid, 'subject_code' => 'ENG', 'subject_name' => 'English Language', 'category' => 'Core'],
            ['school_id' => $sid, 'subject_code' => 'MTC', 'subject_name' => 'Mathematics', 'category' => 'Core'],
            ['school_id' => $sid, 'subject_code' => 'BIO', 'subject_name' => 'Biology', 'category' => 'Core'],
            ['school_id' => $sid, 'subject_code' => 'CHE', 'subject_name' => 'Chemistry', 'category' => 'Core'],
            ['school_id' => $sid, 'subject_code' => 'PHY', 'subject_name' => 'Physics', 'category' => 'Core'],
            ['school_id' => $sid, 'subject_code' => 'GEO', 'subject_name' => 'Geography', 'category' => 'Core'],
            ['school_id' => $sid, 'subject_code' => 'HIS', 'subject_name' => 'History and Political Education', 'category' => 'Core'],
            ['school_id' => $sid, 'subject_code' => 'CRE', 'subject_name' => 'Christian Religious Education', 'category' => 'Elective'],
            ['school_id' => $sid, 'subject_code' => 'ICT', 'subject_name' => 'Information & Communications Technology', 'category' => 'Core'],
            ['school_id' => $sid, 'subject_code' => 'AGR', 'subject_name' => 'Agriculture', 'category' => 'Elective'],
            ['school_id' => $sid, 'subject_code' => 'ENT', 'subject_name' => 'Entrepreneurship Education', 'category' => 'Core'],
            ['school_id' => $sid, 'subject_code' => 'PE', 'subject_name' => 'Physical Education', 'category' => 'Core'],
            ['school_id' => $sid, 'subject_code' => 'KIS', 'subject_name' => 'Kiswahili', 'category' => 'Core'],
        ]);

        // Class Subjects (S1 gets all Core)
        $coreSubjects = Subject::where('category', 'Core')->pluck('id');
        foreach ($coreSubjects as $subjectId) {
            ClassSubject::create(['class_level_id' => 1, 'subject_id' => $subjectId, 'is_compulsory' => true]);
        }

        // Curriculum Themes - S1 Mathematics
        $mathsTheme = CurriculumTheme::create([
            'subject_id' => 2,
            'class_level_id' => 1,
            'theme_code' => 'MTC-S1-T1',
            'theme_name' => 'Numerical Concepts 1',
            'description' => 'Numbers, place value, operations on integers, fractions and ratios',
        ]);

        LearningOutcome::insert([
            ['theme_id' => $mathsTheme->id, 'outcome_code' => 'MTC-S1-T1-LO1', 'description' => 'The learner performs operations on integers, fractions and decimals correctly'],
            ['theme_id' => $mathsTheme->id, 'outcome_code' => 'MTC-S1-T1-LO2', 'description' => 'The learner applies ratios and proportions to solve everyday problems'],
        ]);

        // Generic Skills
        GenericSkill::insert([
            ['skill_name' => 'Communication', 'description' => 'Ability to express ideas clearly in speech and writing, and to listen and read with understanding'],
            ['skill_name' => 'Cooperation and Self-Directed Learning', 'description' => "Ability to work effectively with others and to take responsibility for one's own learning"],
            ['skill_name' => 'Critical Thinking and Problem Solving', 'description' => 'Ability to analyse situations and apply knowledge to solve problems'],
            ['skill_name' => 'Creativity and Innovation', 'description' => 'Ability to generate and apply new ideas in practical ways'],
        ]);

        // Assessment Types
        AssessmentType::insert([
            ['type_name' => 'Continuous Assessment Test', 'category' => 'Formative', 'weight_percentage' => 0],
            ['type_name' => 'Project Work / Activity of Integration', 'category' => 'Formative', 'weight_percentage' => 0],
            ['type_name' => 'End of Topic Assessment', 'category' => 'Formative', 'weight_percentage' => 0],
            ['type_name' => 'End of Term Exam', 'category' => 'Summative', 'weight_percentage' => 0],
            ['type_name' => 'End of Cycle Exam (UCE)', 'category' => 'Summative', 'weight_percentage' => 80],
        ]);

        // Grading Scale
        GradingScale::insert([
            ['grade' => 'A', 'descriptor' => 'Exceptional', 'min_score' => 80.00, 'max_score' => 100.00, 'remarks' => 'Advanced competency - applies knowledge innovatively'],
            ['grade' => 'B', 'descriptor' => 'Outstanding', 'min_score' => 70.00, 'max_score' => 79.99, 'remarks' => 'High competency - applies skills effectively'],
            ['grade' => 'C', 'descriptor' => 'Satisfactory', 'min_score' => 55.00, 'max_score' => 69.99, 'remarks' => 'Adequate knowledge and skill application'],
            ['grade' => 'D', 'descriptor' => 'Basic', 'min_score' => 40.00, 'max_score' => 54.99, 'remarks' => 'Minimum competency - limited practical application'],
            ['grade' => 'E', 'descriptor' => 'Elementary', 'min_score' => 0.00, 'max_score' => 39.99, 'remarks' => 'Beginning level - difficulty applying knowledge'],
        ]);

        // Skill Rating Scale
        SkillRatingScale::insert([
            ['rating_code' => 'BEG', 'rating_label' => 'Beginning', 'rating_value' => 1, 'description' => 'Learner is starting to develop this skill, needs close support'],
            ['rating_code' => 'DEV', 'rating_label' => 'Developing', 'rating_value' => 2, 'description' => 'Learner shows the skill with some support'],
            ['rating_code' => 'PRO', 'rating_label' => 'Proficient', 'rating_value' => 3, 'description' => 'Learner applies the skill independently'],
            ['rating_code' => 'MAS', 'rating_label' => 'Mastery', 'rating_value' => 4, 'description' => 'Learner applies the skill consistently and helps others'],
        ]);

        // Students
        $student1 = Student::create([
            'admission_no' => 'S26-0001',
            'first_name' => 'Faith',
            'last_name' => 'Achieng',
            'gender' => 'Female',
            'dob' => '2012-03-14',
            'admission_date' => '2026-02-03',
            'status' => 'active',
        ]);

        $student2 = Student::create([
            'admission_no' => 'S26-0002',
            'first_name' => 'Daniel',
            'last_name' => 'Wasswa',
            'gender' => 'Male',
            'dob' => '2012-07-22',
            'admission_date' => '2026-02-03',
            'status' => 'active',
        ]);

        // Enrollments
        Enrollment::insert([
            ['student_id' => 1, 'stream_id' => 1, 'academic_year_id' => 1, 'enrollment_date' => '2026-02-03', 'status' => 'active'],
            ['student_id' => 2, 'stream_id' => 1, 'academic_year_id' => 1, 'enrollment_date' => '2026-02-03', 'status' => 'active'],
        ]);

        // Guardians
        Guardian::insert([
            ['first_name' => 'Moses', 'last_name' => 'Achieng', 'relationship' => 'Father', 'phone' => '+256700000001', 'occupation' => 'Trader'],
            ['first_name' => 'Susan', 'last_name' => 'Wasswa', 'relationship' => 'Mother', 'phone' => '+256700000002', 'occupation' => 'Teacher'],
        ]);

        StudentGuardian::insert([
            ['student_id' => 1, 'guardian_id' => 1, 'is_primary_contact' => true],
            ['student_id' => 2, 'guardian_id' => 2, 'is_primary_contact' => true],
        ]);

        // Subject Term Results for Faith (student_id=1, term_id=1)
        $subjectResults = [
            ['subject_id' => 1, 'ca_score' => 16.00, 'eot_score' => 60.00, 'final_score' => 76.00, 'final_grade' => 'B', 'subject_teacher_comment' => 'Writes clearly and confidently. Work on punctuation in essays.'],
            ['subject_id' => 2, 'ca_score' => 18.00, 'eot_score' => 68.00, 'final_score' => 86.00, 'final_grade' => 'A', 'subject_teacher_comment' => 'Excellent grasp of numerical concepts. Keep it up.'],
            ['subject_id' => 3, 'ca_score' => 14.00, 'eot_score' => 50.00, 'final_score' => 64.00, 'final_grade' => 'C', 'subject_teacher_comment' => 'Understands basic concepts; needs more practice with diagrams.'],
            ['subject_id' => 4, 'ca_score' => 15.00, 'eot_score' => 58.00, 'final_score' => 73.00, 'final_grade' => 'B', 'subject_teacher_comment' => 'Good understanding of practical work.'],
            ['subject_id' => 5, 'ca_score' => 13.00, 'eot_score' => 45.00, 'final_score' => 58.00, 'final_grade' => 'C', 'subject_teacher_comment' => 'Needs to revise topics on forces and motion.'],
            ['subject_id' => 6, 'ca_score' => 17.00, 'eot_score' => 62.00, 'final_score' => 79.00, 'final_grade' => 'B', 'subject_teacher_comment' => 'Good map work skills.'],
            ['subject_id' => 7, 'ca_score' => 12.00, 'eot_score' => 44.00, 'final_score' => 56.00, 'final_grade' => 'C', 'subject_teacher_comment' => 'Should read more widely around historical themes.'],
            ['subject_id' => 9, 'ca_score' => 19.00, 'eot_score' => 70.00, 'final_score' => 89.00, 'final_grade' => 'A', 'subject_teacher_comment' => 'Confident with computer practicals and typing skills.'],
            ['subject_id' => 11, 'ca_score' => 16.00, 'eot_score' => 55.00, 'final_score' => 71.00, 'final_grade' => 'B', 'subject_teacher_comment' => 'Shows good business sense in class activities.'],
            ['subject_id' => 12, 'ca_score' => 18.00, 'eot_score' => 64.00, 'final_score' => 82.00, 'final_grade' => 'A', 'subject_teacher_comment' => 'Active and disciplined during games.'],
            ['subject_id' => 13, 'ca_score' => 14.00, 'eot_score' => 48.00, 'final_score' => 62.00, 'final_grade' => 'C', 'subject_teacher_comment' => 'Participates well in conversation practice.'],
        ];

        foreach ($subjectResults as $result) {
            SubjectTermResult::create(array_merge(
                $result,
                ['student_id' => 1, 'term_id' => 1]
            ));
        }

        // Generic Skill Ratings for Faith
        GenericSkillRating::insert([
            ['student_id' => 1, 'term_id' => 1, 'generic_skill_id' => 1, 'rating_id' => 3, 'remarks' => 'Speaks up confidently during class discussions'],
            ['student_id' => 1, 'term_id' => 1, 'generic_skill_id' => 2, 'rating_id' => 4, 'remarks' => 'Works very well in groups and supports peers'],
            ['student_id' => 1, 'term_id' => 1, 'generic_skill_id' => 3, 'rating_id' => 2, 'remarks' => 'Still developing confidence in solving novel problems'],
            ['student_id' => 1, 'term_id' => 1, 'generic_skill_id' => 4, 'rating_id' => 3, 'remarks' => 'Comes up with creative ideas for class projects'],
        ]);

        // Report Card for Faith
        ReportCard::create([
            'student_id' => 1,
            'term_id' => 1,
            'stream_id' => 1,
            'days_present' => 58,
            'days_absent' => 2,
            'class_teacher_comment' => 'Faith is a hardworking and well-behaved learner who participates actively in class. She should pay more attention during Physics practicals.',
            'head_teacher_comment' => 'A promising start to the year. Keep up the good work, Faith.',
            'next_term_begins' => '2026-05-25',
            'date_issued' => '2026-05-08',
        ]);

        $this->call(SampleAccountSeeder::class);
    }
}
