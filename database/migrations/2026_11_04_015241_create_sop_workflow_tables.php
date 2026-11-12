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
        // 1. Table sops
        Schema::create('sops', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sop');
            $table->string('nomor_dokumen')->unique();
            $table->foreignId('department_id')->constrained('divisions')->cascadeOnDelete();
            $table->foreignId('bod_id')->nullable()->constrained('bods')->nullOnDelete();
            $table->date('tanggal_efektif');
            $table->mediumText('tujuan');
            $table->mediumText('ruang_lingkup');
            $table->mediumText('definisi');
            $table->mediumText('aturan_umum');
            $table->string('status')->default('draft'); // draft, menunggu_persetujuan, ditolak, disetujui
            $table->string('version')->default('1.0');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
        });

        // 2. Table sop_revision_notes
        Schema::create('sop_revision_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sop_id')->constrained('sops')->cascadeOnDelete();
            $table->string('nomor_revisi');
            $table->date('tanggal_revisi');
            $table->text('keterangan');
            $table->timestamps();
        });

        // 3. Table sop_attachments
        Schema::create('sop_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sop_id')->constrained('sops')->cascadeOnDelete();
            $table->string('jenis_file'); // flowchart, lampiran
            $table->string('nama_file');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // 4. Table sop_rejection_reasons
        Schema::create('sop_rejection_reasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sop_id')->constrained('sops')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->text('alasan_penolakan');
            $table->timestamp('created_at')->useCurrent();
        });

        // 5. Table sop_versions
        Schema::create('sop_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sop_id')->constrained('sops')->cascadeOnDelete();
            $table->string('version_number');
            $table->json('data_snapshot');
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->text('change_description')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // 6. Table notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sop_id')->nullable()->constrained('sops')->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->string('type'); // sop_submission, sop_approved, sop_rejected, sop_resubmitted
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('sop_versions');
        Schema::dropIfExists('sop_rejection_reasons');
        Schema::dropIfExists('sop_attachments');
        Schema::dropIfExists('sop_revision_notes');
        Schema::dropIfExists('sops');
    }
};
