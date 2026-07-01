<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_types', function (Blueprint $table) {
            $table->id();
            $table->string('type_name', 60);
            $table->string('category', 20);
            $table->decimal('weight_percentage', 5, 2)->default(0);
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('grading_scale', function (Blueprint $table) {
            $table->id();
            $table->string('grade', 2)->unique();
            $table->string('descriptor', 30);
            $table->decimal('min_score', 5, 2);
            $table->decimal('max_score', 5, 2);
            $table->string('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('skill_rating_scale', function (Blueprint $table) {
            $table->id();
            $table->string('rating_code', 5)->unique();
            $table->string('rating_label', 30);
            $table->tinyInteger('rating_value');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('assessment_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('theme_id')->nullable()->constrained('curriculum_themes')->nullOnDelete();
            $table->foreignId('learning_outcome_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assessment_type_id')->constrained('assessment_types');
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 5, 2)->nullable();
            $table->decimal('max_score', 5, 2)->default(100);
            $table->string('grade', 2)->nullable();
            $table->string('remarks')->nullable();
            $table->date('date_recorded');
            $table->foreignId('recorded_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('generic_skill_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->foreignId('generic_skill_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rating_id')->constrained('skill_rating_scale');
            $table->string('remarks')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->timestamps();
            $table->unique(['student_id', 'term_id', 'generic_skill_id']);
        });

        Schema::create('subject_term_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->decimal('ca_score', 5, 2)->nullable();
            $table->decimal('eot_score', 5, 2)->nullable();
            $table->decimal('final_score', 5, 2)->nullable();
            $table->string('final_grade', 2)->nullable();
            $table->string('subject_teacher_comment')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'subject_id', 'term_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_term_results');
        Schema::dropIfExists('generic_skill_ratings');
        Schema::dropIfExists('assessment_records');
        Schema::dropIfExists('skill_rating_scale');
        Schema::dropIfExists('grading_scale');
        Schema::dropIfExists('assessment_types');
    }
};
