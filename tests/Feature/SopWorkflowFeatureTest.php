<?php

namespace Tests\Feature;

use App\Models\Bod;
use App\Models\Division;
use App\Models\Notification;
use App\Models\Sop;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SopWorkflowFeatureTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $admin;
    protected User $anggota;
    protected Division $department;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // Create or find BOD & Department
        $bod = Bod::firstOrCreate(
            ['name' => 'COO TEST'],
            ['color' => '#3b82f6', 'order' => 1]
        );

        $this->department = Division::firstOrCreate(
            ['name' => 'HRD TEST', 'bod_id' => $bod->id],
            ['color' => '#3b82f6', 'order' => 1]
        );

        // Create Users with distinct roles
        $this->superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->anggota = User::factory()->create([
            'role' => 'anggota',
            'is_active' => true,
        ]);
    }

    /**
     * Skenario 1: Admin dapat membuat dan menyimpan Draft SOP
     */
    public function test_admin_can_create_draft_sop(): void
    {
        $response = $this->actingAs($this->admin)->post(route('sops.store'), [
            'action' => 'draft',
            'nama_sop' => 'SOP Penerimaan Karyawan',
            'nomor_dokumen' => 'SOP-HRD-001-TEST',
            'department_id' => $this->department->id,
            'tanggal_efektif' => '2026-11-04',
            'tujuan' => 'Tujuan SOP rekrutmen',
            'ruang_lingkup' => 'Seluruh proses rekrutmen',
            'definisi' => 'Karyawan baru: staf baru diterima',
            'aturan_umum' => 'Wajib mematuhi etika kerja',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('sops', [
            'nomor_dokumen' => 'SOP-HRD-001-TEST',
            'status' => Sop::STATUS_DRAFT,
            'created_by' => $this->admin->id,
        ]);
    }

    /**
     * Skenario 2 & 3 & 4: Admin mengajukan SOP -> status Menunggu Persetujuan -> Super Admin menerima notifikasi
     */
    public function test_admin_can_submit_sop_and_super_admin_receives_notification(): void
    {
        $flowchart = UploadedFile::fake()->image('flowchart.png');
        $attachment = UploadedFile::fake()->create('form_rekrutmen.pdf', 100);

        $response = $this->actingAs($this->admin)->post(route('sops.store'), [
            'action' => 'submit',
            'nama_sop' => 'SOP Onboarding Karyawan',
            'nomor_dokumen' => 'SOP-HRD-002-TEST',
            'department_id' => $this->department->id,
            'tanggal_efektif' => '2026-11-04',
            'tujuan' => 'Memastikan onboarding terstruktur',
            'ruang_lingkup' => 'Departemen HRD dan user',
            'definisi' => 'Onboarding: masa orientasi',
            'aturan_umum' => 'Dilakukan 3 hari kerja pertama',
            'flowchart' => $flowchart,
            'lampiran' => [$attachment],
            'revision_notes' => [
                [
                    'nomor_revisi' => '1.0',
                    'tanggal_revisi' => '2026-11-04',
                    'keterangan' => 'Penerbitan perdana',
                ]
            ],
        ]);

        $response->assertRedirect();
        
        $sop = Sop::where('nomor_dokumen', 'SOP-HRD-002-TEST')->first();
        $this->assertNotNull($sop);
        $this->assertEquals(Sop::STATUS_PENDING, $sop->status);
        $this->assertNotNull($sop->flowchart);
        $this->assertCount(1, $sop->regularAttachments);

        // Notifikasi untuk Super Admin
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->superAdmin->id,
            'sop_id' => $sop->id,
            'type' => 'sop_submission',
        ]);
    }

    /**
     * Skenario 8 & 9 & 10: Super Admin menolak SOP dengan alasan wajib -> Admin menerima notifikasi penolakan
     */
    public function test_super_admin_can_reject_sop_with_mandatory_reason(): void
    {
        $sop = Sop::create([
            'nama_sop' => 'SOP Penggajian Karyawan',
            'nomor_dokumen' => 'SOP-HRD-003-TEST',
            'department_id' => $this->department->id,
            'bod_id' => $this->department->bod_id,
            'tanggal_efektif' => '2026-11-04',
            'tujuan' => 'Tujuan',
            'ruang_lingkup' => 'Ruang lingkup',
            'definisi' => 'Definisi',
            'aturan_umum' => 'Aturan',
            'status' => Sop::STATUS_PENDING,
            'version' => '1.0',
            'created_by' => $this->admin->id,
            'submitted_at' => now(),
        ]);

        // Coba tolak tanpa alasan -> harus error validasi
        $responseFail = $this->actingAs($this->superAdmin)->post(route('sops.reject', $sop->id), [
            'alasan_penolakan' => '',
        ]);
        $responseFail->assertSessionHasErrors('alasan_penolakan');

        // Tolak dengan alasan valid
        $reasonText = 'Bagian ruang lingkup belum menjelaskan cut-off payroll secara rinci. Mohon lengkapi.';
        $responseOk = $this->actingAs($this->superAdmin)->post(route('sops.reject', $sop->id), [
            'alasan_penolakan' => $reasonText,
        ]);

        $responseOk->assertRedirect();
        $sop->refresh();

        $this->assertEquals(Sop::STATUS_REJECTED, $sop->status);
        $this->assertEquals($this->superAdmin->id, $sop->rejected_by);
        $this->assertNotNull($sop->rejected_at);

        // Riwayat penolakan tersimpan
        $this->assertDatabaseHas('sop_rejection_reasons', [
            'sop_id' => $sop->id,
            'reviewer_id' => $this->superAdmin->id,
            'alasan_penolakan' => $reasonText,
        ]);

        // Notifikasi ke Admin pengaju
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->admin->id,
            'sop_id' => $sop->id,
            'type' => 'sop_rejected',
        ]);
    }

    /**
     * Skenario 11, 12, 13: Admin merevisi SOP yang ditolak dan mengajukan kembali
     */
    public function test_admin_can_revise_and_resubmit_rejected_sop(): void
    {
        $sop = Sop::create([
            'nama_sop' => 'SOP Pelatihan Internal',
            'nomor_dokumen' => 'SOP-HRD-004-TEST',
            'department_id' => $this->department->id,
            'bod_id' => $this->department->bod_id,
            'tanggal_efektif' => '2026-11-04',
            'tujuan' => 'Tujuan lama',
            'ruang_lingkup' => 'Ruang lingkup lama',
            'definisi' => 'Definisi lama',
            'aturan_umum' => 'Aturan lama',
            'status' => Sop::STATUS_REJECTED,
            'version' => '1.0',
            'created_by' => $this->admin->id,
            'rejected_by' => $this->superAdmin->id,
            'rejected_at' => now(),
        ]);

        // Simpan alasan penolakan sebelumnya
        $sop->rejectionReasons()->create([
            'reviewer_id' => $this->superAdmin->id,
            'alasan_penolakan' => 'Lengkapi bagian aturan umum terkait biaya training.',
            'created_at' => now(),
        ]);

        // Admin revisi & ajukan kembali
        $newFlowchart = UploadedFile::fake()->image('flowchart_v2.png');
        $response = $this->actingAs($this->admin)->put(route('sops.update', $sop->id), [
            'action' => 'submit',
            'nama_sop' => 'SOP Pelatihan Internal - Direvisi',
            'nomor_dokumen' => 'SOP-HRD-004-TEST',
            'department_id' => $this->department->id,
            'tanggal_efektif' => '2026-11-04',
            'tujuan' => 'Tujuan baru yang disempurnakan',
            'ruang_lingkup' => 'Ruang lingkup baru',
            'definisi' => 'Definisi lengkap',
            'aturan_umum' => 'Aturan umum dan klausul biaya training telah ditambahkan',
            'flowchart' => $newFlowchart,
            'revision_notes' => [
                [
                    'nomor_revisi' => '1.0',
                    'tanggal_revisi' => '2026-11-01',
                    'keterangan' => 'Rilis perdana',
                ],
                [
                    'nomor_revisi' => '1.1',
                    'tanggal_revisi' => '2026-11-04',
                    'keterangan' => 'Revisi klausul biaya training sesuai arahan Super Admin',
                ]
            ],
        ]);

        $response->assertRedirect();
        $sop->refresh();

        // Status kembali MENUNGGU PERSETUJUAN
        $this->assertEquals(Sop::STATUS_PENDING, $sop->status);
        $this->assertEquals('1.1', $sop->version);
        $this->assertEquals('SOP Pelatihan Internal - Direvisi', $sop->nama_sop);

        // Alasan penolakan sebelumnya tetap tersimpan
        $this->assertCount(1, $sop->rejectionReasons);

        // Notifikasi ke Super Admin
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->superAdmin->id,
            'sop_id' => $sop->id,
            'type' => 'sop_resubmitted',
        ]);
    }

    /**
     * Skenario 6 & 7: Super Admin menyetujui SOP -> status Disetujui -> resmi dipublikasikan
     */
    public function test_super_admin_can_approve_sop(): void
    {
        $sop = Sop::create([
            'nama_sop' => 'SOP Cuti Karyawan',
            'nomor_dokumen' => 'SOP-HRD-005-TEST',
            'department_id' => $this->department->id,
            'bod_id' => $this->department->bod_id,
            'tanggal_efektif' => '2026-11-04',
            'tujuan' => 'Prosedur cuti tahunan dan khusus',
            'ruang_lingkup' => 'Seluruh karyawan',
            'definisi' => 'Cuti tahunan: hak cuti 12 hari per tahun',
            'aturan_umum' => 'Pengajuan H-7 kerja',
            'status' => Sop::STATUS_PENDING,
            'version' => '1.0',
            'created_by' => $this->admin->id,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('sops.approve', $sop->id));
        $response->assertRedirect();

        $sop->refresh();
        $this->assertEquals(Sop::STATUS_APPROVED, $sop->status);
        $this->assertEquals($this->superAdmin->id, $sop->approved_by);
        $this->assertNotNull($sop->approved_at);

        // Notifikasi ke Admin pengaju
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->admin->id,
            'sop_id' => $sop->id,
            'type' => 'sop_approved',
        ]);

        // Dapat diakses oleh Anggota
        $responseAnggota = $this->actingAs($this->anggota)->get(route('sops.show', $sop->id));
        $responseAnggota->assertOk();
    }

    /**
     * Skenario 14, 15, 16: Anggota & Admin Authorization Restrictions
     */
    public function test_authorization_restrictions(): void
    {
        // 1. SOP pending tidak boleh dilihat oleh Anggota
        $pendingSop = Sop::create([
            'nama_sop' => 'SOP Rahasia Belum Disetujui',
            'nomor_dokumen' => 'SOP-HRD-006-TEST',
            'department_id' => $this->department->id,
            'bod_id' => $this->department->bod_id,
            'tanggal_efektif' => '2026-11-04',
            'tujuan' => 'Tujuan',
            'ruang_lingkup' => 'Ruang lingkup',
            'definisi' => 'Definisi',
            'aturan_umum' => 'Aturan',
            'status' => Sop::STATUS_PENDING,
            'version' => '1.0',
            'created_by' => $this->admin->id,
        ]);

        $this->actingAs($this->anggota)->get(route('sops.show', $pendingSop->id))->assertForbidden();

        // 2. Admin tidak boleh menyetujui SOP
        $this->actingAs($this->admin)->post(route('sops.approve', $pendingSop->id))->assertForbidden();

        // 3. Admin tidak boleh menolak SOP
        $this->actingAs($this->admin)->post(route('sops.reject', $pendingSop->id), [
            'alasan_penolakan' => 'Coba tolak',
        ])->assertForbidden();

        // 4. Anggota tidak boleh membuka form create SOP
        $this->actingAs($this->anggota)->get(route('sops.create'))->assertForbidden();

        // 5. Anggota tidak boleh POST store SOP
        $this->actingAs($this->anggota)->post(route('sops.store'), [
            'nama_sop' => 'SOP Ilegal Anggota',
        ])->assertForbidden();

        // 6. Anggota mengakses /sops dialihkan ke direktori SOP resmi
        $this->actingAs($this->anggota)->get(route('sops.index'))->assertRedirect(route('documents.index', 'sop'));
    }

    /**
     * Skenario 17 & 18: Download attachment, preview flowchart, print view
     */
    public function test_attachment_download_and_printable_view(): void
    {
        $flowchart = UploadedFile::fake()->image('flowchart.png');
        $attachment = UploadedFile::fake()->create('form_cuti.pdf', 50);

        $sop = Sop::create([
            'nama_sop' => 'SOP Cuti Pegawai',
            'nomor_dokumen' => 'SOP-HRD-007-TEST',
            'department_id' => $this->department->id,
            'bod_id' => $this->department->bod_id,
            'tanggal_efektif' => '2026-11-04',
            'tujuan' => 'Tujuan cuti',
            'ruang_lingkup' => 'Cakupan cuti',
            'definisi' => 'Definisi cuti',
            'aturan_umum' => 'Aturan cuti',
            'status' => Sop::STATUS_APPROVED,
            'version' => '1.0',
            'created_by' => $this->admin->id,
            'approved_by' => $this->superAdmin->id,
            'approved_at' => now(),
        ]);

        $attRecord = $sop->attachments()->create([
            'jenis_file' => 'lampiran',
            'nama_file' => 'form_cuti.pdf',
            'file_path' => $attachment->storeAs('sops/' . $sop->id . '/attachments', 'form_cuti.pdf', 'public'),
            'mime_type' => 'application/pdf',
            'file_size' => 50000,
            'uploaded_by' => $this->admin->id,
        ]);

        // Anggota can download attachment of approved SOP
        $responseDownload = $this->actingAs($this->anggota)->get(route('sops.attachments.download', [$sop->id, $attRecord->id]));
        $responseDownload->assertOk();

        // Anggota can view printable official SOP page
        $responsePrint = $this->actingAs($this->anggota)->get(route('sops.print', $sop->id));
        $responsePrint->assertOk();
        $responsePrint->assertSee('STANDARD OPERATING PROCEDURE');
        $responsePrint->assertSee($sop->nomor_dokumen);
    }

    /**
     * Skenario 19: Notifikasi dapat diklik dan menandai status dibaca
     */
    public function test_notification_marking_and_redirect(): void
    {
        $sop = Sop::create([
            'nama_sop' => 'SOP Testing Notifikasi',
            'nomor_dokumen' => 'SOP-HRD-008-TEST',
            'department_id' => $this->department->id,
            'bod_id' => $this->department->bod_id,
            'tanggal_efektif' => '2026-11-04',
            'tujuan' => 'Tujuan',
            'ruang_lingkup' => 'Ruang lingkup',
            'definisi' => 'Definisi',
            'aturan_umum' => 'Aturan',
            'status' => Sop::STATUS_APPROVED,
            'version' => '1.0',
            'created_by' => $this->admin->id,
        ]);

        $notification = Notification::send(
            $this->admin->id,
            'SOP Disetujui!',
            'SOP Anda telah disetujui',
            'sop_approved',
            $sop->id
        );

        $this->assertFalse($notification->isRead());

        // Klik link notifikasi
        $response = $this->actingAs($this->admin)->get(route('notifications.read', $notification->id));
        $response->assertRedirect(route('sops.show', $sop->id));

        $notification->refresh();
        $this->assertTrue($notification->isRead());
    }
}
