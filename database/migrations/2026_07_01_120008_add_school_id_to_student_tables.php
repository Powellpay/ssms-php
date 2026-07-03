<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->constrained()->cascadeOnDelete()->after('id');
        });
        Schema::table('guardians', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->constrained()->cascadeOnDelete()->after('id');
        });
        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->constrained()->cascadeOnDelete()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['school_id']); $table->dropColumn('school_id');
        });
        Schema::table('guardians', function (Blueprint $table) {
            $table->dropForeign(['school_id']); $table->dropColumn('school_id');
        });
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['school_id']); $table->dropColumn('school_id');
        });
    }
};
