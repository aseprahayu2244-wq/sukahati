<?php

namespace App\Http\Controllers;

use App\Models\Bod;
use App\Models\Bpm;
use App\Models\BpmAttachment;
use App\Models\BpmRaci;
use App\Models\BpmRejection;
use App\Models\BpmStep;
use App\Models\BpmVersion;
use App\Models\Division;
use App\Models\Notification;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BpmWorkflowController extends Controller
{
    /**
     * BPM Workflow Hub: List BPMs according to tabs and roles
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Anggota hanya dapat melihat BPM yang sudah disetujui melalui direktori BPM
        if ($user->isAnggota()) {
            return redirect()->route('documents.index', 'bpm');
        }

        // Available tabs: resmi, pengajuan, draft, ditolak
        $tab = $request->get('tab', 'resmi');

        // Query builder
        $query = Bpm::with(['department.bod', 'creator', 'approver', 'rejecter']);

        // Search filter
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_bpm', 'like', "%{$search}%")
                  ->orWhere('nomor_dokumen', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Department filter
        if ($departmentId = $request->get('department_id')) {
            $query->where('department_id', $departmentId);
        }

        // BOD filter
        if ($bodId = $request->get('bod_id')) {
            $query->where('bod_id', $bodId);
        }

        // Source type filter (builder or upload)
        if ($sourceType = $request->get('source_type')) {
            $query->where('source_type', $sourceType);
        }

        // Calculate counts for badges
        $counts = [
            'resmi' => Bpm::where('status', Bpm::STATUS_APPROVED)->count(),
            'pengajuan' => $user->isSuperAdmin()
                ? Bpm::where('status', Bpm::STATUS_PENDING)->count()
                : Bpm::where('status', Bpm::STATUS_PENDING)->where('created_by', $user->id)->count(),
            'draft' => $user->isSuperAdmin()
                ? Bpm::where('status', Bpm::STATUS_DRAFT)->count()
                : Bpm::where('status', Bpm::STATUS_DRAFT)->where('created_by', $user->id)->count(),
            'ditolak' => $user->isSuperAdmin()
                ? Bpm::where('status', Bpm::STATUS_REJECTED)->count()
                : Bpm::where('status', Bpm::STATUS_REJECTED)->where('created_by', $user->id)->count(),
        ];

        // Apply Tab Filter
        switch ($tab) {
            case 'pengajuan':
                $query->where('status', Bpm::STATUS_PENDING);
                if (!$user->isSuperAdmin()) {
                    $query->where('created_by', $user->id);
                }
                $query->latest('submitted_at');
                break;

            case 'draft':
                $query->where('status', Bpm::STATUS_DRAFT);
                if (!$user->isSuperAdmin()) {
                    $query->where('created_by', $user->id);
                }
                $query->latest('updated_at');
                break;

            case 'ditolak':
                $query->where('status', Bpm::STATUS_REJECTED);
                if (!$user->isSuperAdmin()) {
                    $query->where('created_by', $user->id);
                }
                $query->with('latestRejection.reviewer')->latest('rejected_at');
                break;

            case 'resmi':
            default:
                $query->where('status', Bpm::STATUS_APPROVED);
                $query->latest('approved_at');
                break;
        }

        $bpms = $query->paginate(12)->withQueryString();
        $departments = Division::with('bod')->orderBy('name')->get();
        $bods = Bod::orderBy('order')->get();

        return view('bpms.index', compact('bpms', 'tab', 'counts', 'departments', 'bods'));
    }

    /**
     * Show form to create new BPM (choice of Builder or Upload)
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->canManageDocuments(), 403, 'Hanya Admin atau Super Admin yang dapat membuat pengajuan BPM.');

        $mode = $request->get('mode', 'builder'); // 'builder' or 'upload'
        $selectedDepartmentId = $request->get('department_id');
        $departments = Division::with('bod')->orderBy('name')->get();
        $bods = Bod::orderBy('order')->get();
        $positions = Position::orderBy('name')->get();

        return view('bpms.create', compact('mode', 'departments', 'bods', 'positions', 'selectedDepartmentId'));
    }

    /**
     * Store new BPM (either from Builder or Upload)
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->canManageDocuments(), 403);

        $action = $request->input('action', 'submit'); // 'draft' or 'submit'
        $sourceType = $request->input('source_type', Bpm::SOURCE_BUILDER);

        // Validation rules
        $rules = [
            'nama_bpm'        => 'required|string|max:255',
            'nomor_dokumen'   => 'required|string|max:100|unique:bpms,nomor_dokumen',
            'bod_id'          => 'nullable|exists:bods,id',
            'department_id'   => 'required|exists:divisions,id',
            'tanggal_efektif' => 'required|date',
            'deskripsi'       => 'nullable|string',
            'source_type'     => 'required|in:builder,upload',
        ];

        if ($sourceType === Bpm::SOURCE_UPLOAD) {
            $rules['bpm_file'] = ($action === 'submit') ? 'required|file|mimes:pdf,jpg,jpeg,png|max:10240' : 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240';
        }

        $request->validate($rules, [
            'nama_bpm.required'        => 'Nama BPM wajib diisi.',
            'nomor_dokumen.required'   => 'Nomor Dokumen BPM wajib diisi.',
            'nomor_dokumen.unique'     => 'Nomor Dokumen BPM sudah digunakan, masukkan nomor dokumen lain.',
            'department_id.required'   => 'Departemen wajib dipilih.',
            'tanggal_efektif.required' => 'Tanggal Efektif wajib diisi.',
            'bpm_file.required'        => 'File dokumen BPM wajib diunggah.',
            'bpm_file.mimes'           => 'File dokumen BPM harus berformat PDF, JPG, JPEG, atau PNG.',
            'bpm_file.max'             => 'Ukuran file dokumen BPM maksimal 10MB.',
        ]);

        // If submitting from builder, validate L1 presence & descriptions
        if ($sourceType === Bpm::SOURCE_BUILDER && $action === 'submit') {
            $steps = $request->input('steps', []);
            if (empty($steps) || !is_array($steps)) {
                return back()->withInput()->withErrors(['steps' => 'BPM Builder minimal harus memiliki 1 tahapan proses (L1).']);
            }

            $hasL1 = false;
            foreach ($steps as $index => $step) {
                if (($step['level'] ?? '') === 'L1') {
                    $hasL1 = true;
                }
                if (empty(trim($step['activity_description'] ?? ''))) {
                    return back()->withInput()->withErrors(["steps.{$index}.activity_description" => 'Seluruh uraian aktivitas level wajib diisi.']);
                }
            }

            if (!$hasL1) {
                return back()->withInput()->withErrors(['steps' => 'BPM Builder minimal harus memiliki satu baris Level 1 (L1).']);
            }
        }

        // Auto-assign bod_id from department if missing
        $bodId = $request->bod_id;
        if (!$bodId && $request->department_id) {
            $dept = Division::find($request->department_id);
            $bodId = $dept?->bod_id;
        }

        // Determine initial status
        // Super Admin submitting -> approved and published immediately
        // Admin submitting -> pending
        // Draft action -> draft
        if ($action === 'draft') {
            $status = Bpm::STATUS_DRAFT;
            $submittedAt = null;
            $approvedBy = null;
            $approvedAt = null;
        } else {
            if ($user->isSuperAdmin()) {
                $status = Bpm::STATUS_APPROVED;
                $submittedAt = now();
                $approvedBy = $user->id;
                $approvedAt = now();
            } else {
                $status = Bpm::STATUS_PENDING;
                $submittedAt = now();
                $approvedBy = null;
                $approvedAt = null;
            }
        }

        DB::beginTransaction();
        try {
            $bpm = Bpm::create([
                'nama_bpm'        => $request->nama_bpm,
                'nomor_dokumen'   => $request->nomor_dokumen,
                'bod_id'          => $bodId,
                'department_id'   => $request->department_id,
                'tanggal_efektif' => $request->tanggal_efektif,
                'deskripsi'       => $request->deskripsi,
                'source_type'     => $sourceType,
                'status'          => $status,
                'version'         => '1.0',
                'created_by'      => $user->id,
                'submitted_at'    => $submittedAt,
                'approved_by'     => $approvedBy,
                'approved_at'     => $approvedAt,
            ]);

            // Save Builder steps & RACI
            if ($sourceType === Bpm::SOURCE_BUILDER) {
                $this->saveBuilderStepsAndRaci($bpm, $request->input('steps', []));
            }

            // Save Uploaded file
            if ($request->hasFile('bpm_file')) {
                $file = $request->file('bpm_file');
                $storedPath = $file->store('bpm_attachments', 'public');

                BpmAttachment::create([
                    'bpm_id'      => $bpm->id,
                    'file_name'   => $file->getClientOriginalName(),
                    'file_path'   => $storedPath,
                    'mime_type'   => $file->getClientMimeType(),
                    'file_size'   => $file->getSize(),
                    'uploaded_by' => $user->id,
                ]);
            }

            // Also check supporting attachments if any
            if ($request->hasFile('supporting_attachments')) {
                foreach ($request->file('supporting_attachments') as $file) {
                    $storedPath = $file->store('bpm_attachments', 'public');
                    BpmAttachment::create([
                        'bpm_id'      => $bpm->id,
                        'file_name'   => $file->getClientOriginalName(),
                        'file_path'   => $storedPath,
                        'mime_type'   => $file->getClientMimeType(),
                        'file_size'   => $file->getSize(),
                        'uploaded_by' => $user->id,
                    ]);
                }
            }

            // Create initial snapshot
            $bpm->createSnapshot($user, 'Versi awal dokumen BPM.');

            // Notifications
            if ($status === Bpm::STATUS_PENDING) {
                Notification::notifySuperAdmins(
                    'Pengajuan BPM Baru',
                    "Admin {$user->name} mengajukan BPM baru: {$bpm->nama_bpm} ({$bpm->nomor_dokumen}) untuk diperiksa.",
                    'bpm_submission',
                    null,
                    $bpm->id
                );
            }

            DB::commit();

            $msg = match($status) {
                Bpm::STATUS_DRAFT    => 'Draft BPM berhasil disimpan.',
                Bpm::STATUS_APPROVED => 'BPM berhasil dibuat dan langsung dipublikasikan secara resmi.',
                default              => 'BPM berhasil diajukan dan sedang menunggu persetujuan Super Admin.',
            };

            return redirect()->route('bpms.show', $bpm->id)->with('success', $msg);

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan BPM: ' . $e->getMessage());
        }
    }

    /**
     * Show BPM details, preview, and RACI matrix
     */
    public function show(Bpm $bpm)
    {
        $user = auth()->user();
        abort_unless($bpm->canBeViewedBy($user), 403, 'Anda tidak memiliki hak akses untuk melihat BPM ini.');

        $bpm->load([
            'bod',
            'department',
            'creator',
            'approver',
            'rejecter',
            'steps.raci.position',
            'attachments.uploader',
            'rejections.reviewer',
            'versions.user',
        ]);

        return view('bpms.show', compact('bpm'));
    }

    /**
     * Interactive/Clean Preview of BPM Document
     */
    public function preview(Bpm $bpm)
    {
        $user = auth()->user();
        abort_unless($bpm->canBeViewedBy($user), 403);

        $bpm->load([
            'bod',
            'department',
            'creator',
            'approver',
            'steps.raci.position',
            'attachments',
        ]);

        return view('bpms.preview', compact('bpm'));
    }

    /**
     * Printable view / Export to PDF
     */
    public function print(Bpm $bpm)
    {
        $user = auth()->user();
        abort_unless($bpm->canBeViewedBy($user), 403);

        $bpm->load([
            'bod',
            'department',
            'creator',
            'approver',
            'steps.raci.position',
            'attachments',
        ]);

        return view('bpms.print', compact('bpm'));
    }

    /**
     * Show form to edit / revise BPM
     */
    public function edit(Bpm $bpm)
    {
        $user = auth()->user();
        abort_unless($bpm->canBeEditedBy($user), 403, 'Anda tidak memiliki hak untuk mengedit BPM ini.');

        $bpm->load([
            'steps.raci',
            'attachments',
            'latestRejection.reviewer',
            'versions.user',
        ]);

        $departments = Division::with('bod')->orderBy('name')->get();
        $bods = Bod::orderBy('order')->get();
        $positions = Position::orderBy('name')->get();

        return view('bpms.edit', compact('bpm', 'departments', 'bods', 'positions'));
    }

    /**
     * Update / Revise BPM
     */
    public function update(Request $request, Bpm $bpm)
    {
        $user = auth()->user();
        abort_unless($bpm->canBeEditedBy($user), 403);

        $action = $request->input('action', 'submit'); // 'draft' or 'submit'
        $sourceType = $request->input('source_type', $bpm->source_type);

        $rules = [
            'nama_bpm'        => 'required|string|max:255',
            'nomor_dokumen'   => 'required|string|max:100|unique:bpms,nomor_dokumen,' . $bpm->id,
            'bod_id'          => 'nullable|exists:bods,id',
            'department_id'   => 'required|exists:divisions,id',
            'tanggal_efektif' => 'required|date',
            'deskripsi'       => 'nullable|string',
        ];

        if ($sourceType === Bpm::SOURCE_UPLOAD && $bpm->attachments()->count() === 0) {
            $rules['bpm_file'] = ($action === 'submit') ? 'required|file|mimes:pdf,jpg,jpeg,png|max:10240' : 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240';
        } else {
            $rules['bpm_file'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240';
        }

        $request->validate($rules, [
            'nama_bpm.required'        => 'Nama BPM wajib diisi.',
            'nomor_dokumen.required'   => 'Nomor Dokumen BPM wajib diisi.',
            'nomor_dokumen.unique'     => 'Nomor Dokumen BPM sudah digunakan.',
            'department_id.required'   => 'Departemen wajib dipilih.',
            'tanggal_efektif.required' => 'Tanggal Efektif wajib diisi.',
            'bpm_file.mimes'           => 'File dokumen BPM harus berformat PDF, JPG, JPEG, atau PNG.',
            'bpm_file.max'             => 'Ukuran file dokumen BPM maksimal 10MB.',
        ]);

        // Validate builder if submitting
        if ($sourceType === Bpm::SOURCE_BUILDER && $action === 'submit') {
            $steps = $request->input('steps', []);
            if (empty($steps) || !is_array($steps)) {
                return back()->withInput()->withErrors(['steps' => 'BPM Builder minimal harus memiliki 1 tahapan proses (L1).']);
            }

            $hasL1 = false;
            foreach ($steps as $index => $step) {
                if (($step['level'] ?? '') === 'L1') {
                    $hasL1 = true;
                }
                if (empty(trim($step['activity_description'] ?? ''))) {
                    return back()->withInput()->withErrors(["steps.{$index}.activity_description" => 'Seluruh uraian aktivitas level wajib diisi.']);
                }
            }

            if (!$hasL1) {
                return back()->withInput()->withErrors(['steps' => 'BPM Builder minimal harus memiliki satu baris Level 1 (L1).']);
            }
        }

        $bodId = $request->bod_id;
        if (!$bodId && $request->department_id) {
            $dept = Division::find($request->department_id);
            $bodId = $dept?->bod_id;
        }

        $wasRejected = $bpm->isRejected();
        $wasApproved = $bpm->isApproved();

        // Status update logic
        if ($action === 'draft') {
            $status = Bpm::STATUS_DRAFT;
        } else {
            if ($user->isSuperAdmin()) {
                $status = Bpm::STATUS_APPROVED;
                $bpm->approved_by = $user->id;
                $bpm->approved_at = now();
            } else {
                $status = Bpm::STATUS_PENDING;
                $bpm->submitted_at = now();
            }
        }

        // Increment version number on resubmission / major update
        $newVersion = $bpm->version;
        if ($action === 'submit' && ($wasRejected || $wasApproved)) {
            $parts = explode('.', $bpm->version);
            if (count($parts) === 2) {
                $newVersion = $parts[0] . '.' . ((int)$parts[1] + 1);
            } else {
                $newVersion = '2.0';
            }
        }

        DB::beginTransaction();
        try {
            $bpm->update([
                'nama_bpm'        => $request->nama_bpm,
                'nomor_dokumen'   => $request->nomor_dokumen,
                'bod_id'          => $bodId,
                'department_id'   => $request->department_id,
                'tanggal_efektif' => $request->tanggal_efektif,
                'deskripsi'       => $request->deskripsi,
                'source_type'     => $sourceType,
                'status'          => $status,
                'version'         => $newVersion,
            ]);

            // Save Builder steps & RACI
            if ($sourceType === Bpm::SOURCE_BUILDER) {
                $this->saveBuilderStepsAndRaci($bpm, $request->input('steps', []));
            }

            // Upload new file if provided
            if ($request->hasFile('bpm_file')) {
                $file = $request->file('bpm_file');
                $storedPath = $file->store('bpm_attachments', 'public');

                BpmAttachment::create([
                    'bpm_id'      => $bpm->id,
                    'file_name'   => $file->getClientOriginalName(),
                    'file_path'   => $storedPath,
                    'mime_type'   => $file->getClientMimeType(),
                    'file_size'   => $file->getSize(),
                    'uploaded_by' => $user->id,
                ]);
            }

            // Record snapshot
            $desc = $wasRejected ? 'Pengajuan kembali setelah revisi penolakan.' : 'Pembaruan dokumen BPM.';
            $bpm->createSnapshot($user, $desc);

            // Notifications
            if ($status === Bpm::STATUS_PENDING) {
                $notifType = $wasRejected ? 'bpm_resubmitted' : 'bpm_submission';
                $title = $wasRejected ? 'Pengajuan Kembali BPM (Revisi)' : 'Pengajuan BPM';
                $msg = $wasRejected
                    ? "Admin {$user->name} telah merevisi dan mengajukan kembali BPM: {$bpm->nama_bpm} ({$bpm->nomor_dokumen})."
                    : "Admin {$user->name} mengajukan BPM: {$bpm->nama_bpm} ({$bpm->nomor_dokumen}) untuk diperiksa.";

                Notification::notifySuperAdmins($title, $msg, $notifType, null, $bpm->id);
            }

            DB::commit();

            return redirect()->route('bpms.show', $bpm->id)->with('success', 'BPM berhasil diperbarui.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui BPM: ' . $e->getMessage());
        }
    }

    /**
     * Super Admin: Approve BPM
     */
    public function approve(Request $request, Bpm $bpm)
    {
        $user = auth()->user();
        abort_unless($user->isSuperAdmin(), 403, 'Hanya Super Admin yang berhak menyetujui BPM.');
        abort_unless($bpm->isPending(), 400, 'Hanya BPM berstatus Menunggu Persetujuan yang dapat disetujui.');

        $bpm->update([
            'status'      => Bpm::STATUS_APPROVED,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        $bpm->createSnapshot($user, 'Disetujui dan dipublikasikan resmi oleh Super Admin.');

        // Notify submitter Admin
        if ($bpm->created_by && $bpm->created_by !== $user->id) {
            Notification::send(
                $bpm->created_by,
                'BPM Telah Disetujui!',
                "Kabar baik! Pengajuan BPM '{$bpm->nama_bpm}' ({$bpm->nomor_dokumen}) telah disetujui oleh Super Admin {$user->name} dan dipublikasikan secara resmi.",
                'bpm_approved',
                null,
                $bpm->id
            );
        }

        return redirect()->route('bpms.show', $bpm->id)->with('success', 'BPM berhasil disetujui dan dipublikasikan secara resmi.');
    }

    /**
     * Super Admin: Reject BPM with required reason
     */
    public function reject(Request $request, Bpm $bpm)
    {
        $user = auth()->user();
        abort_unless($user->isSuperAdmin(), 403, 'Hanya Super Admin yang berhak menolak BPM.');
        abort_unless($bpm->isPending(), 400, 'Hanya BPM berstatus Menunggu Persetujuan yang dapat ditolak.');

        $request->validate([
            'alasan_penolakan' => 'required|string|min:5',
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi untuk memberikan instruksi revisi yang jelas.',
            'alasan_penolakan.min'      => 'Alasan penolakan minimal 5 karakter.',
        ]);

        DB::beginTransaction();
        try {
            $bpm->update([
                'status'      => Bpm::STATUS_REJECTED,
                'rejected_by' => $user->id,
                'rejected_at' => now(),
            ]);

            BpmRejection::create([
                'bpm_id'           => $bpm->id,
                'reviewer_id'      => $user->id,
                'rejection_reason' => $request->alasan_penolakan,
                'created_at'       => now(),
            ]);

            $bpm->createSnapshot($user, 'Ditolak dengan alasan: ' . $request->alasan_penolakan);

            // Notify submitter Admin
            if ($bpm->created_by) {
                Notification::send(
                    $bpm->created_by,
                    'Pengajuan BPM Memerlukan Revisi',
                    "Pengajuan BPM '{$bpm->nama_bpm}' ({$bpm->nomor_dokumen}) ditolak oleh Super Admin. Alasan: \"{$request->alasan_penolakan}\". Silakan lakukan perbaikan.",
                    'bpm_rejected',
                    null,
                    $bpm->id
                );
            }

            DB::commit();

            return redirect()->route('bpms.show', $bpm->id)->with('success', 'BPM telah ditolak dan catatan alasan penolakan berhasil dikirimkan ke Admin pengaju.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menolak BPM: ' . $e->getMessage());
        }
    }

    /**
     * Delete BPM
     */
    public function destroy(Bpm $bpm)
    {
        $user = auth()->user();
        abort_unless($bpm->canBeDeletedBy($user), 403, 'Anda tidak berhak menghapus BPM ini.');

        // Delete attachment files from storage
        foreach ($bpm->attachments as $att) {
            Storage::disk('public')->delete($att->file_path);
        }

        $bpm->delete();

        return redirect()->route('bpms.index')->with('success', 'BPM berhasil dihapus.');
    }

    /**
     * Download attachment file
     */
    public function downloadAttachment(Bpm $bpm, BpmAttachment $attachment)
    {
        $user = auth()->user();
        abort_unless($bpm->canBeViewedBy($user), 403);
        abort_unless($attachment->bpm_id === $bpm->id, 404);

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'Berkas fisik tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }

    /**
     * Preview attachment in browser
     */
    public function previewAttachment(Bpm $bpm, BpmAttachment $attachment)
    {
        $user = auth()->user();
        abort_unless($bpm->canBeViewedBy($user), 403);
        abort_unless($attachment->bpm_id === $bpm->id, 404);

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'Berkas fisik tidak ditemukan di server.');
        }

        $path = Storage::disk('public')->path($attachment->file_path);
        return response()->file($path, [
            'Content-Type' => $attachment->mime_type ?? mime_content_type($path),
            'Content-Disposition' => 'inline; filename="' . $attachment->file_name . '"',
        ]);
    }

    /**
     * Helper to save builder steps and RACI assignments in order
     */
    private function saveBuilderStepsAndRaci(Bpm $bpm, array $stepsInput): void
    {
        // Remove old steps & raci (cascade will remove raci)
        $bpm->steps()->delete();

        if (empty($stepsInput)) {
            return;
        }

        $sortOrder = 0;
        foreach ($stepsInput as $stepData) {
            $level = in_array($stepData['level'] ?? '', ['L1', 'L2', 'L3']) ? $stepData['level'] : 'L1';
            $activityDesc = trim($stepData['activity_description'] ?? '');

            if ($activityDesc === '') {
                continue;
            }

            $step = BpmStep::create([
                'bpm_id'               => $bpm->id,
                'level'                => $level,
                'activity_description' => $activityDesc,
                'sort_order'           => $sortOrder++,
                'parent_id'            => null,
            ]);

            // Save RACI
            // R: Responsible (array of position IDs)
            if (!empty($stepData['raci_r']) && is_array($stepData['raci_r'])) {
                foreach ($stepData['raci_r'] as $posId) {
                    if (!empty($posId)) {
                        BpmRaci::create([
                            'bpm_step_id'         => $step->id,
                            'responsibility_type' => 'R',
                            'position_id'         => (int)$posId,
                        ]);
                    }
                }
            }

            // A: Accountable (single position ID default)
            if (!empty($stepData['raci_a'])) {
                $posId = is_array($stepData['raci_a']) ? ($stepData['raci_a'][0] ?? null) : $stepData['raci_a'];
                if (!empty($posId)) {
                    BpmRaci::create([
                        'bpm_step_id'         => $step->id,
                        'responsibility_type' => 'A',
                        'position_id'         => (int)$posId,
                    ]);
                }
            }

            // C: Consulted (array of position IDs)
            if (!empty($stepData['raci_c']) && is_array($stepData['raci_c'])) {
                foreach ($stepData['raci_c'] as $posId) {
                    if (!empty($posId)) {
                        BpmRaci::create([
                            'bpm_step_id'         => $step->id,
                            'responsibility_type' => 'C',
                            'position_id'         => (int)$posId,
                        ]);
                    }
                }
            }

            // I: Informed (array of position IDs)
            if (!empty($stepData['raci_i']) && is_array($stepData['raci_i'])) {
                foreach ($stepData['raci_i'] as $posId) {
                    if (!empty($posId)) {
                        BpmRaci::create([
                            'bpm_step_id'         => $step->id,
                            'responsibility_type' => 'I',
                            'position_id'         => (int)$posId,
                        ]);
                    }
                }
            }
        }
    }
}

