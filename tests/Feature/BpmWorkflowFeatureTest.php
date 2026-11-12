<?php

namespace Tests\Feature;

use App\Models\Bod;
use App\Models\Bpm;
use App\Models\BpmAttachment;
use App\Models\BpmStep;
use App\Models\Division;
use App\Models\Notification;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BpmWorkflowFeatureTest extends TestCase
{
    use DatabaseTransactions;

    private User $superAdmin;
    private User $admin;
    private User $anggota;
    private Bod $bod;
    private Division $dept;
    private Position $pos1;
    private Position $pos2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Roles
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

        // Create BOD, Department, Positions
        $this->bod = Bod::create(['name' => 'BOD Operasional', 'order' => 1]);
        $this->dept = Division::create(['name' => 'Human Resources', 'bod_id' => $this->bod->id]);
        $this->pos1 = Position::create(['name' => 'HR Manager', 'division_id' => $this->dept->id]);
        $this->pos2 = Position::create(['name' => 'Staff HRD', 'division_id' => $this->dept->id]);
    }

    /**
     * 1. Admin can create draft BPM with L1, L2, L3 and RACI
     */
    public function test_admin_can_create_draft_bpm_builder(): void
    {
        $response = $this->actingAs($this->admin)->post(route('bpms.store'), [
            'action' => 'draft',
            'source_type' => 'builder',
            'nama_bpm' => 'Proses Rekrutmen Karyawan',
            'nomor_dokumen' => 'BPM-HRD-001',
            'bod_id' => $this->bod->id,
            'department_id' => $this->dept->id,
            'tanggal_efektif' => '2026-11-12',
            'deskripsi' => 'Alur proses rekrutmen pegawai baru.',
            'steps' => [
                [
                    'level' => 'L1',
                    'activity_description' => 'Proses Perencanaan Kebutuhan',
                    'raci_r' => [$this->pos1->id],
                    'raci_a' => $this->pos1->id,
                    'raci_c' => [$this->pos2->id],
                    'raci_i' => [$this->pos2->id],
                ],
                [
                    'level' => 'L2',
                    'activity_description' => 'Identifikasi Formasi Lowongan',
                    'raci_r' => [$this->pos2->id],
                    'raci_a' => $this->pos1->id,
                ],
                [
                    'level' => 'L3',
                    'activity_description' => 'Membuat Form Permintaan Tenaga Kerja (FPTK)',
                    'raci_r' => [$this->pos2->id],
                    'raci_a' => $this->pos1->id,
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('bpms', [
            'nama_bpm' => 'Proses Rekrutmen Karyawan',
            'nomor_dokumen' => 'BPM-HRD-001',
            'status' => 'draft',
            'created_by' => $this->admin->id,
        ]);

        $this->assertDatabaseCount('bpm_steps', 3);
        $this->assertDatabaseCount('bpm_raci', 8);
    }

    /**
     * 2. Admin can submit BPM and Super Admin receives in-app notification
     */
    public function test_admin_can_submit_bpm_and_super_admin_receives_notification(): void
    {
        $response = $this->actingAs($this->admin)->post(route('bpms.store'), [
            'action' => 'submit',
            'source_type' => 'builder',
            'nama_bpm' => 'Pengadaan Aset Kantor',
            'nomor_dokumen' => 'BPM-HRD-002',
            'bod_id' => $this->bod->id,
            'department_id' => $this->dept->id,
            'tanggal_efektif' => '2026-11-12',
            'steps' => [
                [
                    'level' => 'L1',
                    'activity_description' => 'Perencanaan Pengadaan Aset',
                    'raci_r' => [$this->pos1->id],
                    'raci_a' => $this->pos1->id,
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();

        $bpm = Bpm::where('nomor_dokumen', 'BPM-HRD-002')->first();
        $this->assertNotNull($bpm);
        $this->assertEquals(Bpm::STATUS_PENDING, $bpm->status);
        $this->assertNotNull($bpm->submitted_at);

        // Check notification delivered to Super Admin
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->superAdmin->id,
            'bpm_id' => $bpm->id,
            'type' => 'bpm_submission',
        ]);
    }

    /**
     * 3. Super Admin can reject BPM with required reason and Admin receives notification
     */
    public function test_super_admin_can_reject_bpm_with_reason(): void
    {
        $bpm = Bpm::create([
            'nama_bpm' => 'BPM Uji Penolakan',
            'nomor_dokumen' => 'BPM-HRD-REJ',
            'bod_id' => $this->bod->id,
            'department_id' => $this->dept->id,
            'tanggal_efektif' => '2026-11-12',
            'source_type' => 'builder',
            'status' => Bpm::STATUS_PENDING,
            'version' => '1.0',
            'created_by' => $this->admin->id,
            'submitted_at' => now(),
        ]);

        // Attempt rejection without reason should fail validation
        $responseFail = $this->actingAs($this->superAdmin)->post(route('bpms.reject', $bpm->id), [
            'alasan_penolakan' => '',
        ]);
        $responseFail->assertSessionHasErrors('alasan_penolakan');

        // Valid rejection
        $response = $this->actingAs($this->superAdmin)->post(route('bpms.reject', $bpm->id), [
            'alasan_penolakan' => 'Hierarki L2 belum memiliki aktivitas rincian L3.',
        ]);

        $response->assertSessionHasNoErrors();
        $bpm->refresh();

        $this->assertEquals(Bpm::STATUS_REJECTED, $bpm->status);
        $this->assertNotNull($bpm->rejected_at);
        $this->assertEquals($this->superAdmin->id, $bpm->rejected_by);

        // Check rejection record & notification
        $this->assertDatabaseHas('bpm_rejections', [
            'bpm_id' => $bpm->id,
            'reviewer_id' => $this->superAdmin->id,
            'rejection_reason' => 'Hierarki L2 belum memiliki aktivitas rincian L3.',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->admin->id,
            'bpm_id' => $bpm->id,
            'type' => 'bpm_rejected',
        ]);
    }

    /**
     * 4. Admin can revise and resubmit rejected BPM without losing ID
     */
    public function test_admin_can_revise_and_resubmit_rejected_bpm(): void
    {
        $bpm = Bpm::create([
            'nama_bpm' => 'BPM Uji Revisi',
            'nomor_dokumen' => 'BPM-HRD-REV',
            'bod_id' => $this->bod->id,
            'department_id' => $this->dept->id,
            'tanggal_efektif' => '2026-11-12',
            'source_type' => 'builder',
            'status' => Bpm::STATUS_REJECTED,
            'version' => '1.0',
            'created_by' => $this->admin->id,
            'submitted_at' => now(),
            'rejected_by' => $this->superAdmin->id,
            'rejected_at' => now(),
        ]);

        $originalId = $bpm->id;

        $response = $this->actingAs($this->admin)->put(route('bpms.update', $bpm->id), [
            'action' => 'submit',
            'source_type' => 'builder',
            'nama_bpm' => 'BPM Uji Revisi (Diperbaiki)',
            'nomor_dokumen' => 'BPM-HRD-REV',
            'bod_id' => $this->bod->id,
            'department_id' => $this->dept->id,
            'tanggal_efektif' => '2026-11-12',
            'steps' => [
                [
                    'level' => 'L1',
                    'activity_description' => 'L1 Revisi',
                    'raci_r' => [$this->pos1->id],
                    'raci_a' => $this->pos1->id,
                ],
                [
                    'level' => 'L2',
                    'activity_description' => 'L2 Subproses Lengkap',
                    'raci_r' => [$this->pos2->id],
                ],
                [
                    'level' => 'L3',
                    'activity_description' => 'L3 Aktivitas Detail Ditambahkan',
                    'raci_r' => [$this->pos2->id],
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $bpm->refresh();

        $this->assertEquals($originalId, $bpm->id);
        $this->assertEquals(Bpm::STATUS_PENDING, $bpm->status);
        $this->assertEquals('1.1', $bpm->version); // version incremented

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->superAdmin->id,
            'bpm_id' => $bpm->id,
            'type' => 'bpm_resubmitted',
        ]);
    }

    /**
     * 5. Super Admin can approve BPM and publish it
     */
    public function test_super_admin_can_approve_bpm(): void
    {
        $bpm = Bpm::create([
            'nama_bpm' => 'BPM Uji Persetujuan',
            'nomor_dokumen' => 'BPM-HRD-APP',
            'bod_id' => $this->bod->id,
            'department_id' => $this->dept->id,
            'tanggal_efektif' => '2026-11-12',
            'source_type' => 'builder',
            'status' => Bpm::STATUS_PENDING,
            'version' => '1.0',
            'created_by' => $this->admin->id,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('bpms.approve', $bpm->id));
        $response->assertSessionHasNoErrors();

        $bpm->refresh();
        $this->assertEquals(Bpm::STATUS_APPROVED, $bpm->status);
        $this->assertEquals($this->superAdmin->id, $bpm->approved_by);
        $this->assertNotNull($bpm->approved_at);

        // Submitting Admin receives approval notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->admin->id,
            'bpm_id' => $bpm->id,
            'type' => 'bpm_approved',
        ]);
    }

    /**
     * 6. Authorization: Anggota cannot view unapproved BPM, cannot create or edit
     */
    public function test_authorization_restrictions(): void
    {
        $pendingBpm = Bpm::create([
            'nama_bpm' => 'BPM Pending',
            'nomor_dokumen' => 'BPM-PEND',
            'bod_id' => $this->bod->id,
            'department_id' => $this->dept->id,
            'tanggal_efektif' => '2026-11-12',
            'source_type' => 'builder',
            'status' => Bpm::STATUS_PENDING,
            'version' => '1.0',
            'created_by' => $this->admin->id,
        ]);

        // Anggota cannot access BPM hub
        $this->actingAs($this->anggota)->get(route('bpms.index'))->assertRedirect(route('documents.index', 'bpm'));

        // Anggota cannot view pending BPM details
        $this->actingAs($this->anggota)->get(route('bpms.show', $pendingBpm->id))->assertForbidden();

        // Anggota cannot approve or reject
        $this->actingAs($this->anggota)->post(route('bpms.approve', $pendingBpm->id))->assertForbidden();
        $this->actingAs($this->anggota)->post(route('bpms.reject', $pendingBpm->id), ['alasan_penolakan' => 'test'])->assertForbidden();

        // Admin cannot approve or reject
        $this->actingAs($this->admin)->post(route('bpms.approve', $pendingBpm->id))->assertForbidden();
        $this->actingAs($this->admin)->post(route('bpms.reject', $pendingBpm->id), ['alasan_penolakan' => 'test'])->assertForbidden();
    }

    /**
     * 7. Super Admin can directly create and publish approved BPM
     */
    public function test_super_admin_can_create_and_directly_publish_bpm(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('bpms.store'), [
            'action' => 'submit',
            'source_type' => 'builder',
            'nama_bpm' => 'BPM Langsung Publikasi Super Admin',
            'nomor_dokumen' => 'BPM-SA-DIRECT',
            'bod_id' => $this->bod->id,
            'department_id' => $this->dept->id,
            'tanggal_efektif' => '2026-11-12',
            'steps' => [
                [
                    'level' => 'L1',
                    'activity_description' => 'L1 Direct',
                    'raci_r' => [$this->pos1->id],
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $bpm = Bpm::where('nomor_dokumen', 'BPM-SA-DIRECT')->first();
        $this->assertNotNull($bpm);
        $this->assertEquals(Bpm::STATUS_APPROVED, $bpm->status);
        $this->assertEquals($this->superAdmin->id, $bpm->approved_by);
    }

    /**
     * 8. Upload-based BPM works seamlessly alongside BPM Builder
     */
    public function test_upload_based_bpm_works(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('sop_proses.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->admin)->post(route('bpms.store'), [
            'action' => 'submit',
            'source_type' => 'upload',
            'nama_bpm' => 'BPM Metode Upload File',
            'nomor_dokumen' => 'BPM-UPL-001',
            'bod_id' => $this->bod->id,
            'department_id' => $this->dept->id,
            'tanggal_efektif' => '2026-11-12',
            'bpm_file' => $file,
        ]);

        $response->assertSessionHasNoErrors();
        $bpm = Bpm::where('nomor_dokumen', 'BPM-UPL-001')->first();
        $this->assertNotNull($bpm);
        $this->assertEquals(Bpm::SOURCE_UPLOAD, $bpm->source_type);
        $this->assertCount(1, $bpm->attachments);
    }
}
