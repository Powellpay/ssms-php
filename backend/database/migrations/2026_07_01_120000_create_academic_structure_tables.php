<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('year_name', 9)->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });

        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('term_name', 10);
            $table->date('start_date');
            $table->date('end_date');
            $table->date('next_term_begins')->nullable();
            $table->boolean('is_current')->default(false);
            $table->timestamps();
            $table->unique(['academic_year_id', 'term_name']);
        });

        Schema::create('class_levels', function (Blueprint $table) {
            $table->id();
            $table->string('level_name', 20)->unique();
            $table->tinyInteger('numeric_level');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_levels');
        Schema::dropIfExists('terms');
        Schema::dropIfExists('academic_years');
    }
};
