<?php

namespace App\Http\Controllers;

use App\Models\Bod;
use App\Models\Division;
use App\Models\Notification;
use App\Models\Sop;
use App\Models\SopAttachment;
use App\Models\SopRejectionReason;
use App\Models\SopRevisionNote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SopWorkflowController extends Controller
{
    /**
     * SOP Workflow Hub: List SOPs according to tabs and roles
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Anggota hanya dapat melihat SOP yang sudah disetujui & dipublikasikan melalui direktori SOP
        if ($user->isAnggota()) {
            return redirect()->route('documents.index', 'sop');
        }

        // Available tabs: resmi, pengajuan, draft, ditolak
        $tab = $request->get('tab', 'resmi');

        // Query builder
        $query = Sop::with(['department.bod', 'creator', 'approver', 'rejecter']);

        // Search filter
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_sop', 'like', "%{$search}%")
                  ->orWhere('nomor_dokumen', 'like', "%{$search}%")
                  ->orWhere('tujuan', 'like', "%{$search}%");
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

        // Calculate counts for badges
        $counts = [
            'resmi' => Sop::where('status', Sop::STATUS_APPROVED)->count(),
            'pengajuan' => $user->isSuperAdmin()
                ? Sop::where('status', Sop::STATUS_PENDING)->count()
                : Sop::where('status', Sop::STATUS_PENDING)->where('created_by', $user->id)->count(),
            'draft' => $user->isSuperAdmin()
                ? Sop::where('status', Sop::STATUS_DRAFT)->count()
                : Sop::where('status', Sop::STATUS_DRAFT)->where('created_by', $user->id)->count(),
            'ditolak' => $user->isSuperAdmin()
                ? Sop::where('status', Sop::STATUS_REJECTED)->count()
                : Sop::where('status', Sop::STATUS_REJECTED)->where('created_by', $user->id)->count(),
        ];

        // Apply Tab Filter
        switch ($tab) {
            case 'pengajuan':
                $query->where('status', Sop::STATUS_PENDING);
                if (!$user->isSuperAdmin()) {
                    $query->where('created_by', $user->id);
                }
                $query->latest('submitted_at');
                break;

            case 'draft':
                $query->where('status', Sop::STATUS_DRAFT);
                if (!$user->isSuperAdmin()) {
                    $query->where('created_by', $user->id);
                }
                $query->latest('updated_at');
                break;

            case 'ditolak':
                $query->where('status', Sop::STATUS_REJECTED);
                if (!$user->isSuperAdmin()) {
                    $query->where('created_by', $user->id);
                }
                $query->with('latestRejectionReason.reviewer')->latest('rejected_at');
                break;

            case 'resmi':
            default:
                $query->where('status', Sop::STATUS_APPROVED);
                $query->latest('approved_at');
                break;
        }

        $sops = $query->paginate(12)->withQueryString();
        $departments = Division::with('bod')->orderBy('name')->get();
        $bods = Bod::orderBy('order')->get();

        return view('sops.index', compact('sops', 'tab', 'counts', 'departments', 'bods'));
    }

    /**
     * Show form to create new SOP
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->canManageDocuments(), 403, 'Hanya Admin atau Super Admin yang dapat membuat pengajuan SOP.');

        $selectedDepartmentId = $request->get('department_id');
        $departments = Division::with('bod')->orderBy('name')->get();

        return view('sops.create', compact('departments', 'selectedDepartmentId'));
    }

    /**
     * Store new SOP
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        abort_unless($user->canManageDocuments(), 403, 'Hanya Admin atau Super Admin yang dapat membuat pengajuan SOP.');

        $action = $request->input('action', 'submit'); // 'draft', 'submit', or 'approve'

        $rules = [
            'nama_sop' => 'required|string|max:255',
            'nomor_dokumen' => 'required|string|max:100|unique:sops,nomor_dokumen',
            'department_id' => 'required|exists:divisions,id',
            'tanggal_efektif' => 'required|date',
            'tujuan' => 'required|string',
            'ruang_lingkup' => 'required|string',
            'definisi' => 'required|string',
            'aturan_umum' => 'required|string',
            'flowchart' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:20480',
            'lampiran.*' => 'nullable|file|mimes:pdf,png,jpg,jpeg,webp,doc,docx,xls,xlsx,ppt,pptx,zip,rar|max:20480',
            'revision_notes' => 'nullable|array',
            'revision_notes.*.nomor_revisi' => 'nullable|string|max:50',
            'revision_notes.*.tanggal_revisi' => 'nullable|date',
            'revision_notes.*.keterangan' => 'nullable|string',
        ];

        // Flowchart is mandatory if submitting (not draft)
        if ($action !== 'draft') {
            $rules['flowchart'] = 'required|file|mimes:png,jpg,jpeg,pdf|max:20480';
        }

        $validated = $request->validate($rules, [
            'flowchart.required' => 'File Flowchart wajib diunggah sebelum SOP diajukan.',
            'nomor_dokumen.unique' => 'Nomor dokumen sudah digunakan. Mohon gunakan nomor dokumen lain.',
        ]);

        $sop = DB::transaction(function () use ($request, $user, $action, $validated) {
            $dept = Division::findOrFail($request->department_id);

            // Determine status
            $status = Sop::STATUS_DRAFT;
            $approvedBy = null;
            $approvedAt = null;
            $submittedAt = null;

            if ($action === 'submit') {
                if ($user->isSuperAdmin()) {
                    // Super admin can submit directly as approved or pending
                    $status = Sop::STATUS_APPROVED;
                    $approvedBy = $user->id;
                    $approvedAt = now();
                    $submittedAt = now();
                } else {
                    $status = Sop::STATUS_PENDING;
                    $submittedAt = now();
                }
            } elseif ($action === 'approve' && $user->isSuperAdmin()) {
                $status = Sop::STATUS_APPROVED;
                $approvedBy = $user->id;
                $approvedAt = now();
                $submittedAt = now();
            }

            $sop = Sop::create([
                'nama_sop' => $request->nama_sop,
                'nomor_dokumen' => $request->nomor_dokumen,
                'department_id' => $dept->id,
                'bod_id' => $dept->bod_id,
                'tanggal_efektif' => $request->tanggal_efektif,
                'tujuan' => $request->tujuan,
                'ruang_lingkup' => $request->ruang_lingkup,
                'definisi' => $request->definisi,
                'aturan_umum' => $request->aturan_umum,
                'status' => $status,
                'version' => '1.0',
                'created_by' => $user->id,
                'submitted_at' => $submittedAt,
                'approved_by' => $approvedBy,
                'approved_at' => $approvedAt,
            ]);

            // Save Revision Notes
            if ($request->has('revision_notes') && is_array($request->revision_notes)) {
                foreach ($request->revision_notes as $note) {
                    if (!empty($note['nomor_revisi']) || !empty($note['keterangan'])) {
                        $sop->revisionNotes()->create([
                            'nomor_revisi' => $note['nomor_revisi'] ?? '1.0',
                            'tanggal_revisi' => $note['tanggal_revisi'] ?? now()->toDateString(),
                            'keterangan' => $note['keterangan'] ?? 'Pembuatan dokumen awal',
                        ]);
                    }
                }
            } else {
                // Default initial note
                $sop->revisionNotes()->create([
                    'nomor_revisi' => '1.0',
                    'tanggal_revisi' => $request->tanggal_efektif,
                    'keterangan' => 'Penerbitan SOP Perdana',
                ]);
            }

            // Save Flowchart
            if ($request->hasFile('flowchart')) {
                $file = $request->file('flowchart');
                $filename = time() . '_flowchart_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('sops/' . $sop->id . '/flowchart', $filename, 'public');

                $sop->attachments()->create([
                    'jenis_file' => 'flowchart',
                    'nama_file' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => $user->id,
                ]);
            }

            // Save Lampiran (multiple files)
            if ($request->hasFile('lampiran')) {
                foreach ($request->file('lampiran') as $file) {
                    $filename = time() . '_' . Str::random(6) . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('sops/' . $sop->id . '/attachments', $filename, 'public');

                    $sop->attachments()->create([
                        'jenis_file' => 'lampiran',
                        'nama_file' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => $user->id,
                    ]);
                }
            }

            // Create initial version snapshot
            $sop->createSnapshot($user, $action === 'draft' ? 'Draft SOP dibuat' : 'Pengajuan SOP perdana');

            // Send notification if pending
            if ($status === Sop::STATUS_PENDING) {
                Notification::notifySuperAdmins(
                    'Pengajuan SOP Baru',
                    "Admin {$user->name} mengajukan SOP baru: \"{$sop->nama_sop}\" ({$sop->nomor_dokumen}) untuk Departemen {$dept->name}.",
                    'sop_submission',
                    $sop->id
                );
            }

            return $sop;
        });

        $message = match ($sop->status) {
            Sop::STATUS_DRAFT => 'Draft SOP berhasil disimpan. Anda dapat melanjutkannya kapan saja.',
            Sop::STATUS_PENDING => 'SOP berhasil diajukan dan sedang menunggu pemeriksaan Super Admin.',
            Sop::STATUS_APPROVED => 'SOP berhasil dibuat dan langsung dipublikasikan.',
            default => 'SOP berhasil disimpan.',
        };

        return redirect()->route('sops.show', $sop->id)->with('success', $message);
    }

    /**
     * Show SOP detail
     */
    public function show(Sop $sop)
    {
        $user = auth()->user();
        abort_unless($sop->canBeViewedBy($user), 403, 'Anda tidak memiliki hak akses untuk melihat SOP ini.');

        // Mark related notifications as read
        Notification::where('sop_id', $sop->id)
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $sop->load([
            'department.bod',
            'creator',
            'approver',
            'rejecter',
            'revisionNotes',
            'attachments.uploader',
            'flowchart',
            'regularAttachments',
            'rejectionReasons.reviewer',
            'versions.user',
        ]);

        return view('sops.show', compact('sop'));
    }

    /**
     * Show edit / revise form
     */
    public function edit(Sop $sop)
    {
        $user = auth()->user();
        abort_unless($sop->canBeEditedBy($user), 403, 'Anda tidak dapat mengedit SOP dengan status saat ini.');

        $sop->load(['department.bod', 'revisionNotes', 'flowchart', 'regularAttachments', 'latestRejectionReason.reviewer']);
        $departments = Division::with('bod')->orderBy('name')->get();

        return view('sops.edit', compact('sop', 'departments'));
    }

    /**
     * Update or Resubmit SOP
     */
    public function update(Request $request, Sop $sop)
    {
        $user = auth()->user();
        abort_unless($sop->canBeEditedBy($user), 403, 'Anda tidak dapat mengedit SOP dengan status saat ini.');

        $action = $request->input('action', 'submit'); // 'draft', 'submit', or 'approve'

        $rules = [
            'nama_sop' => 'required|string|max:255',
            'nomor_dokumen' => 'required|string|max:100|unique:sops,nomor_dokumen,' . $sop->id,
            'department_id' => 'required|exists:divisions,id',
            'tanggal_efektif' => 'required|date',
            'tujuan' => 'required|string',
            'ruang_lingkup' => 'required|string',
            'definisi' => 'required|string',
            'aturan_umum' => 'required|string',
            'flowchart' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:20480',
            'lampiran.*' => 'nullable|file|mimes:pdf,png,jpg,jpeg,webp,doc,docx,xls,xlsx,ppt,pptx,zip,rar|max:20480',
            'revision_notes' => 'nullable|array',
            'revision_notes.*.nomor_revisi' => 'nullable|string|max:50',
            'revision_notes.*.tanggal_revisi' => 'nullable|date',
            'revision_notes.*.keterangan' => 'nullable|string',
        ];

        // Flowchart check: if submitting, either an existing flowchart exists or a new one is uploaded
        if ($action !== 'draft' && !$sop->flowchart && !$request->hasFile('flowchart')) {
            $rules['flowchart'] = 'required|file|mimes:png,jpg,jpeg,pdf|max:20480';
        }

        $validated = $request->validate($rules, [
            'flowchart.required' => 'File Flowchart wajib diunggah sebelum SOP diajukan.',
            'nomor_dokumen.unique' => 'Nomor dokumen sudah digunakan. Mohon gunakan nomor dokumen lain.',
        ]);

        DB::transaction(function () use ($request, $user, $sop, $action) {
            $dept = Division::findOrFail($request->department_id);
            $wasRejected = $sop->isRejected();

            // Determine status
            $status = $sop->status;
            if ($action === 'draft') {
                $status = Sop::STATUS_DRAFT;
            } elseif ($action === 'submit') {
                if ($user->isSuperAdmin()) {
                    $status = Sop::STATUS_APPROVED;
                    $sop->approved_by = $user->id;
                    $sop->approved_at = now();
                    $sop->submitted_at = now();
                } else {
                    $status = Sop::STATUS_PENDING;
                    $sop->submitted_at = now();
                }
            } elseif ($action === 'approve' && $user->isSuperAdmin()) {
                $status = Sop::STATUS_APPROVED;
                $sop->approved_by = $user->id;
                $sop->approved_at = now();
            }

            // Calculate version bump if revising rejected
            $newVersion = $sop->version;
            if ($wasRejected && $action === 'submit') {
                // Increment minor version (e.g. 1.0 -> 1.1)
                $parts = explode('.', $sop->version);
                if (count($parts) === 2 && is_numeric($parts[1])) {
                    $newVersion = $parts[0] . '.' . ((int)$parts[1] + 1);
                } else {
                    $newVersion = '1.1';
                }
            }

            $sop->update([
                'nama_sop' => $request->nama_sop,
                'nomor_dokumen' => $request->nomor_dokumen,
                'department_id' => $dept->id,
                'bod_id' => $dept->bod_id,
                'tanggal_efektif' => $request->tanggal_efektif,
                'tujuan' => $request->tujuan,
                'ruang_lingkup' => $request->ruang_lingkup,
                'definisi' => $request->definisi,
                'aturan_umum' => $request->aturan_umum,
                'status' => $status,
                'version' => $newVersion,
            ]);

            // Synchronize Revision Notes
            $sop->revisionNotes()->delete();
            if ($request->has('revision_notes') && is_array($request->revision_notes)) {
                foreach ($request->revision_notes as $note) {
                    if (!empty($note['nomor_revisi']) || !empty($note['keterangan'])) {
                        $sop->revisionNotes()->create([
                            'nomor_revisi' => $note['nomor_revisi'] ?? $newVersion,
                            'tanggal_revisi' => $note['tanggal_revisi'] ?? now()->toDateString(),
                            'keterangan' => $note['keterangan'] ?? 'Perubahan dokumen',
                        ]);
                    }
                }
            }

            // Handle Flowchart Replacement
            if ($request->hasFile('flowchart')) {
                // Delete previous flowchart
                if ($oldFlowchart = $sop->flowchart) {
                    Storage::disk('public')->delete($oldFlowchart->file_path);
                    $oldFlowchart->delete();
                }

                $file = $request->file('flowchart');
                $filename = time() . '_flowchart_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('sops/' . $sop->id . '/flowchart', $filename, 'public');

                $sop->attachments()->create([
                    'jenis_file' => 'flowchart',
                    'nama_file' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => $user->id,
                ]);
            }

            // Handle New Lampiran
            if ($request->hasFile('lampiran')) {
                foreach ($request->file('lampiran') as $file) {
                    $filename = time() . '_' . Str::random(6) . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('sops/' . $sop->id . '/attachments', $filename, 'public');

                    $sop->attachments()->create([
                        'jenis_file' => 'lampiran',
                        'nama_file' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => $user->id,
                    ]);
                }
            }

            // Create Version Snapshot
            $desc = $wasRejected && $action === 'submit'
                ? "Revisi SOP setelah penolakan diajukan kembali ke v{$newVersion}"
                : "Pembaruan SOP ({$action})";
            $sop->createSnapshot($user, $desc);

            // Send notification to Super Admins if resubmitted
            if ($status === Sop::STATUS_PENDING && $wasRejected) {
                Notification::notifySuperAdmins(
                    'SOP Direvisi & Diajukan Kembali',
                    "Admin {$user->name} telah merevisi dan mengajukan kembali SOP: \"{$sop->nama_sop}\" (v{$newVersion}).",
                    'sop_resubmitted',
                    $sop->id
                );
            } elseif ($status === Sop::STATUS_PENDING && $action === 'submit') {
                Notification::notifySuperAdmins(
                    'Pengajuan SOP',
                    "Admin {$user->name} mengajukan SOP: \"{$sop->nama_sop}\".",
                    'sop_submission',
                    $sop->id
                );
            }
        });

        $message = match ($sop->status) {
            Sop::STATUS_PENDING => 'SOP berhasil diperbarui dan diajukan ke Super Admin untuk diperiksa.',
            Sop::STATUS_DRAFT => 'Perubahan draft SOP berhasil disimpan.',
            Sop::STATUS_APPROVED => 'SOP berhasil diperbarui dan disetujui.',
            default => 'SOP berhasil diperbarui.',
        };

        return redirect()->route('sops.show', $sop->id)->with('success', $message);
    }

    /**
     * Approve SOP (Super Admin Only)
     */
    public function approve(Request $request, Sop $sop)
    {
        $user = auth()->user();
        abort_unless($user->isSuperAdmin(), 403, 'Hanya Super Admin yang berhak menyetujui SOP.');
        abort_unless($sop->isPending(), 400, 'Hanya SOP dengan status Menunggu Persetujuan yang dapat disetujui.');

        DB::transaction(function () use ($sop, $user) {
            $sop->update([
                'status' => Sop::STATUS_APPROVED,
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

            // Version snapshot
            $sop->createSnapshot($user, "Disetujui dan dipublikasikan resmi oleh Super Admin ({$user->name})");

            // Notification to Creator (Admin)
            if ($sop->created_by) {
                Notification::send(
                    $sop->created_by,
                    'Pengajuan SOP Disetujui! 🎉',
                    "Selamat! Pengajuan SOP Anda \"{$sop->nama_sop}\" ({$sop->nomor_dokumen}) telah disetujui oleh Super Admin dan resmi dipublikasikan.",
                    'sop_approved',
                    $sop->id
                );
            }
        });

        return redirect()->route('sops.show', $sop->id)->with('success', 'SOP berhasil disetujui dan dipublikasikan resmi.');
    }

    /**
     * Reject SOP (Super Admin Only, with mandatory reason)
     */
    public function reject(Request $request, Sop $sop)
    {
        $user = auth()->user();
        abort_unless($user->isSuperAdmin(), 403, 'Hanya Super Admin yang berhak menolak SOP.');
        abort_unless($sop->isPending(), 400, 'Hanya SOP dengan status Menunggu Persetujuan yang dapat ditolak.');

        $request->validate([
            'alasan_penolakan' => 'required|string|min:5',
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi agar Admin dapat memperbaiki SOP.',
            'alasan_penolakan.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $reason = $request->alasan_penolakan;

        DB::transaction(function () use ($sop, $user, $reason) {
            $sop->update([
                'status' => Sop::STATUS_REJECTED,
                'rejected_by' => $user->id,
                'rejected_at' => now(),
            ]);

            // Save rejection history
            SopRejectionReason::create([
                'sop_id' => $sop->id,
                'reviewer_id' => $user->id,
                'alasan_penolakan' => $reason,
                'created_at' => now(),
            ]);

            // Snapshot
            $sop->createSnapshot($user, "Ditolak oleh Super Admin: " . Str::limit($reason, 100));

            // Notify Creator (Admin)
            if ($sop->created_by) {
                Notification::send(
                    $sop->created_by,
                    'Pengajuan SOP Perlu Direvisi (Ditolak)',
                    "Pengajuan SOP \"{$sop->nama_sop}\" memerlukan revisi dari Anda. Alasan: \"{$reason}\"",
                    'sop_rejected',
                    $sop->id
                );
            }
        });

        return redirect()->route('sops.show', $sop->id)->with('success', 'Pengajuan SOP telah ditolak dan catatan revisi telah dikirim ke Admin.');
    }

    /**
     * Delete SOP
     */
    public function destroy(Sop $sop)
    {
        $user = auth()->user();
        abort_unless($sop->canBeDeletedBy($user), 403, 'Anda tidak memiliki hak akses untuk menghapus SOP ini.');

        DB::transaction(function () use ($sop) {
            // Delete files from storage
            foreach ($sop->attachments as $att) {
                Storage::disk('public')->delete($att->file_path);
            }
            Storage::disk('public')->deleteDirectory('sops/' . $sop->id);

            $sop->delete();
        });

        return redirect()->route('sops.index')->with('success', 'SOP berhasil dihapus.');
    }

    /**
     * Download attachment or flowchart
     */
    public function downloadAttachment(Sop $sop, SopAttachment $attachment)
    {
        $user = auth()->user();
        abort_unless($sop->canBeViewedBy($user), 403);
        abort_unless($attachment->sop_id === $sop->id, 404);

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File lampiran tidak ditemukan pada server.');
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->nama_file);
    }

    /**
     * Preview attachment (inline)
     */
    public function previewAttachment(Sop $sop, SopAttachment $attachment)
    {
        $user = auth()->user();
        abort_unless($sop->canBeViewedBy($user), 403);
        abort_unless($attachment->sop_id === $sop->id, 404);

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $fullPath = Storage::disk('public')->path($attachment->file_path);
        return response()->file($fullPath, [
            'Content-Type' => $attachment->mime_type ?? mime_content_type($fullPath),
            'Content-Disposition' => 'inline; filename="' . $attachment->nama_file . '"',
        ]);
    }

    /**
     * Delete individual attachment during edit
     */
    public function deleteAttachment(Sop $sop, SopAttachment $attachment)
    {
        $user = auth()->user();
        abort_unless($sop->canBeEditedBy($user), 403);
        abort_unless($attachment->sop_id === $sop->id, 404);

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return back()->with('success', 'Lampiran berhasil dihapus.');
    }

    /**
     * Official Printable View of SOP Document
     */
    public function print(Sop $sop)
    {
        $user = auth()->user();
        abort_unless($sop->canBeViewedBy($user), 403);

        $sop->load(['department.bod', 'creator', 'approver', 'revisionNotes', 'flowchart', 'regularAttachments']);

        return view('sops.print', compact('sop'));
    }
}

