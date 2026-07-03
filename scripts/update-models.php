<?php

declare(strict_types=1);

$models = [
    'Academic\Models\AcademicYear',
    'Academic\Models\Term',
    'Academic\Models\ClassLevel',
    'Academic\Models\Stream',
    'Curriculum\Models\Subject',
    'Curriculum\Models\ClassSubject',
    'Curriculum\Models\SubjectTeacher',
    'Curriculum\Models\CurriculumTheme',
    'Curriculum\Models\LearningOutcome',
    'Curriculum\Models\GenericSkill',
    'Assessment\Models\AssessmentType',
    'Assessment\Models\GradingScale',
    'Assessment\Models\SkillRatingScale',
    'Assessment\Models\AssessmentRecord',
    'Assessment\Models\GenericSkillRating',
    'Assessment\Models\SubjectTermResult',
    'Reports\Models\ReportCard',
    'Attendance\Models\Attendance',
    'Timetable\Models\Timetable',
    'Finance\Models\FeeStructure',
    'Finance\Models\Invoice',
    'Finance\Models\Payment',
    'Discipline\Models\DisciplineRecord',
    'Library\Models\LibraryBook',
    'Library\Models\BookLoan',
    'Announcements\Models\Announcement',
];

foreach ($models as $m) {
    $shortClass = basename(str_replace('\\', '/', $m));
    $path = 'app/Domain/' . str_replace('\\', '/', $m) . '.php';
    
    if (!file_exists($path)) {
        echo "NOT FOUND: {$path}\n";
        continue;
    }
    
    $c = file_get_contents($path);
    
    $c = str_replace(
        'use Illuminate\Database\Eloquent\Model;',
        "use Illuminate\Database\Eloquent\Model;\nuse App\Domain\Shared\Traits\BelongsToSchool;",
        $c
    );
    
    $c = str_replace(
        "class {$shortClass} extends Model\n{",
        "class {$shortClass} extends Model\n{\n    use BelongsToSchool;",
        $c
    );
    
    $search = "protected \$fillable = [\n";
    if (str_contains($c, $search)) {
        $c = str_replace(
            $search,
            "protected \$fillable = [\n        'school_id',\n",
            $c
        );
    }
    
    file_put_contents($path, $c);
    echo "Updated: {$path}\n";
}

echo "Done.\n";
