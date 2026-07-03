<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'academic_years', 'terms', 'class_levels', 'streams',
        'subjects', 'class_subjects', 'subject_teachers', 'curriculum_themes', 'learning_outcomes', 'generic_skills',
        'assessment_types', 'grading_scale', 'skill_rating_scale', 'assessment_records', 'generic_skill_ratings',
        'subject_term_results',
        'report_cards',
        'attendance',
        'timetable',
        'fee_structures', 'invoices', 'payments',
        'discipline_records',
        'library_books', 'book_loans',
        'announcements',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->foreignId('school_id')->nullable()->constrained()->cascadeOnDelete()->after('id');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['school_id']);
                $table->dropColumn('school_id');
            });
        }
    }
};
