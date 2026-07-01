<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('subject_code', 10)->unique();
            $table->string('subject_name', 60);
            $table->string('category')->default('Core');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('class_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_level_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_compulsory')->default(true);
            $table->timestamps();
            $table->unique(['class_level_id', 'subject_id']);
        });

        Schema::create('subject_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stream_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained();
            $table->timestamps();
            $table->unique(['subject_id', 'stream_id', 'academic_year_id']);
        });

        Schema::create('curriculum_themes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_level_id')->constrained()->cascadeOnDelete();
            $table->string('theme_code', 20)->nullable();
            $table->string('theme_name', 150);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('learning_outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('theme_id')->constrained('curriculum_themes')->cascadeOnDelete();
            $table->string('outcome_code', 20)->nullable();
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('generic_skills', function (Blueprint $table) {
            $table->id();
            $table->string('skill_name', 60)->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generic_skills');
        Schema::dropIfExists('learning_outcomes');
        Schema::dropIfExists('curriculum_themes');
        Schema::dropIfExists('subject_teachers');
        Schema::dropIfExists('class_subjects');
        Schema::dropIfExists('subjects');
    }
};
