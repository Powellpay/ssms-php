<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Module 7: Report Cards
        Schema::create('report_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stream_id')->constrained();
            $table->integer('days_present')->default(0);
            $table->integer('days_absent')->default(0);
            $table->text('class_teacher_comment')->nullable();
            $table->text('head_teacher_comment')->nullable();
            $table->date('next_term_begins')->nullable();
            $table->date('date_issued')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'term_id']);
        });

        // Module 8: Attendance
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->date('attendance_date');
            $table->string('status')->default('Present');
            $table->foreignId('recorded_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->timestamps();
            $table->unique(['student_id', 'attendance_date']);
        });

        // Module 9: Timetable
        Schema::create('timetable', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stream_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained();
            $table->string('day_of_week', 15);
            $table->tinyInteger('period_no');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });

        // Module 10: Finance
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_level_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->string('fee_category', 50);
            $table->decimal('amount', 12, 2);
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_amount', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->virtualAs('total_amount - amount_paid');
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->string('status')->default('unpaid');
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method')->default('Cash');
            $table->string('reference_no', 50)->nullable();
            $table->date('payment_date');
            $table->foreignId('received_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->timestamps();
        });

        // Module 11: Discipline
        Schema::create('discipline_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->date('incident_date');
            $table->text('description');
            $table->string('action_taken')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('staff')->nullOnDelete();
            $table->timestamps();
        });

        // Module 12: Library
        Schema::create('library_books', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('author', 100)->nullable();
            $table->string('isbn', 30)->nullable();
            $table->string('category', 50)->nullable();
            $table->integer('total_copies')->default(1);
            $table->integer('available_copies')->default(1);
            $table->timestamps();
        });

        Schema::create('book_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('library_books')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('borrow_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->string('status')->default('borrowed');
            $table->timestamps();
        });

        // Module 13: Communication
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->text('message');
            $table->string('target_role', 50)->default('all');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('book_loans');
        Schema::dropIfExists('library_books');
        Schema::dropIfExists('discipline_records');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('timetable');
        Schema::dropIfExists('attendance');
        Schema::dropIfExists('report_cards');
    }
};
