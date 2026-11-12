<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Table bpms (Main BPM table)
        Schema::create('bpms', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bpm');
            $table->string('nomor_dokumen')->unique();
            $table->foreignId('bod_id')->nullable()->constrained('bods')->nullOnDelete();
            $table->foreignId('department_id')->constrained('divisions')->cascadeOnDelete();
            $table->date('tanggal_efektif');
            $table->text('deskripsi')->nullable();
            $table->string('source_type')->default('builder'); // 'builder' or 'upload'
            $table->string('status')->default('draft'); // 'draft', 'menunggu_persetujuan', 'ditolak', 'disetujui'
            $table->string('version')->default('1.0');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
        });

        // 2. Table bpm_steps (L1, L2, L3 flexible hierarchy rows)
        Schema::create('bpm_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bpm_id')->constrained('bpms')->cascadeOnDelete();
            $table->enum('level', ['L1', 'L2', 'L3']);
            $table->text('activity_description');
            $table->integer('sort_order')->default(0);
            $table->foreignId('parent_id')->nullable()->constrained('bpm_steps')->nullOnDelete();
            $table->timestamps();
        });

        // 3. Table bpm_raci (RACI assignments for each step)
        Schema::create('bpm_raci', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bpm_step_id')->constrained('bpm_steps')->cascadeOnDelete();
            $table->enum('responsibility_type', ['R', 'A', 'C', 'I']);
            $table->foreignId('position_id')->constrained('positions')->cascadeOnDelete();
            $table->timestamps();
        });

        // 4. Table bpm_attachments (Files for upload-based BPMs or supporting attachments)
        Schema::create('bpm_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bpm_id')->constrained('bpms')->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // 5. Table bpm_rejections (Rejection history)
        Schema::create('bpm_rejections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bpm_id')->constrained('bpms')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->text('rejection_reason');
            $table->timestamp('created_at')->useCurrent();
        });

        // 6. Table bpm_versions (Version history snapshots)
        Schema::create('bpm_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bpm_id')->constrained('bpms')->cascadeOnDelete();
            $table->string('version_number');
            $table->json('data_snapshot');
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->text('change_description')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // 7. Add bpm_id to notifications table if not already present
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                if (!Schema::hasColumn('notifications', 'bpm_id')) {
                    $table->foreignId('bpm_id')->nullable()->after('sop_id')->constrained('bpms')->cascadeOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('notifications') && Schema::hasColumn('notifications', 'bpm_id')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropForeign(['bpm_id']);
                $table->dropColumn('bpm_id');
            });
        }

        Schema::dropIfExists('bpm_versions');
        Schema::dropIfExists('bpm_rejections');
        Schema::dropIfExists('bpm_attachments');
        Schema::dropIfExists('bpm_raci');
        Schema::dropIfExists('bpm_steps');
        Schema::dropIfExists('bpms');
    }
};

