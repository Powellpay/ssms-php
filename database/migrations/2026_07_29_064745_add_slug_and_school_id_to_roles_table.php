<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('slug', 50)->nullable()->after('role_name');
            $table->foreignId('school_id')->nullable()->after('slug')->constrained()->cascadeOnDelete();

            $table->unique(['slug', 'school_id']);
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique(['slug', 'school_id']);
            $table->dropForeign(['school_id']);
            $table->dropColumn(['slug', 'school_id']);
        });
    }
};
