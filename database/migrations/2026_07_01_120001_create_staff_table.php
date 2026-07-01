<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('staff_no', 20)->unique();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('gender', 10);
            $table->date('dob')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('designation', 60)->nullable();
            $table->date('date_joined')->nullable();
            $table->string('photo')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('streams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_level_id')->constrained();
            $table->foreignId('academic_year_id')->constrained();
            $table->string('stream_name', 30);
            $table->foreignId('class_teacher_id')->nullable()->constrained('staff')->nullOnDelete();
            $table->timestamps();
            $table->unique(['class_level_id', 'academic_year_id', 'stream_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('streams');
        Schema::dropIfExists('staff');
    }
};
