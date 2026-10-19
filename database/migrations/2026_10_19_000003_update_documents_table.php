<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add bod_id column to documents and make position_id nullable
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('bod_id')->nullable()->constrained('bods')->onDelete('set null')->after('id');
            $table->foreignId('position_id')->nullable()->change();
        });

        // 2. Safely migrate existing enum values
        \DB::statement("ALTER TABLE documents MODIFY COLUMN type VARCHAR(50) NOT NULL");
        \DB::statement("UPDATE documents SET type = 'job_desk' WHERE type IN ('job', 'job_analysis')");
        \DB::statement("UPDATE documents SET type = 'bpm' WHERE type = 'bpmn'");
        \DB::statement("ALTER TABLE documents MODIFY COLUMN type ENUM('sop', 'job_desk', 'bpm', 'struktur') NOT NULL");
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['bod_id']);
            $table->dropColumn('bod_id');
        });
        \DB::statement("ALTER TABLE documents MODIFY COLUMN type VARCHAR(50) NOT NULL");
        \DB::statement("UPDATE documents SET type = 'job' WHERE type = 'job_desk'");
        \DB::statement("UPDATE documents SET type = 'bpmn' WHERE type = 'bpm'");
        \DB::statement("ALTER TABLE documents MODIFY COLUMN type ENUM('sop', 'job', 'job_analysis', 'bpmn') NOT NULL");
    }
};
