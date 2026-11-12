<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sop extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'menunggu_persetujuan';
    public const STATUS_REJECTED = 'ditolak';
    public const STATUS_APPROVED = 'disetujui';

    protected $fillable = [
        'nama_sop',
        'nomor_dokumen',
        'department_id',
        'bod_id',
        'tanggal_efektif',
        'tujuan',
        'ruang_lingkup',
        'definisi',
        'aturan_umum',
        'status',
        'version',
        'created_by',
        'submitted_at',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
    ];

    protected $casts = [
        'tanggal_efektif' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // =========================================================
    // Relationships
    // =========================================================

    public function department()
    {
        return $this->belongsTo(Division::class, 'department_id');
    }

    public function bod()
    {
        return $this->belongsTo(Bod::class, 'bod_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function revisionNotes()
    {
        return $this->hasMany(SopRevisionNote::class)->orderBy('id');
    }

    public function attachments()
    {
        return $this->hasMany(SopAttachment::class);
    }

    public function flowchart()
    {
        return $this->hasOne(SopAttachment::class)->where('jenis_file', 'flowchart');
    }

    public function regularAttachments()
    {
        return $this->hasMany(SopAttachment::class)->where('jenis_file', 'lampiran');
    }

    public function rejectionReasons()
    {
        return $this->hasMany(SopRejectionReason::class)->latest('id');
    }

    public function latestRejectionReason()
    {
        return $this->hasOne(SopRejectionReason::class)->latestOfMany();
    }

    public function versions()
    {
        return $this->hasMany(SopVersion::class)->latest('id');
    }

    // =========================================================
    // Status Helpers
    // =========================================================

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT   => 'Draft',
            self::STATUS_PENDING => 'Menunggu Persetujuan',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_APPROVED => 'Disetujui',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT   => 'bg-slate-100 text-slate-700 border border-slate-200',
            self::STATUS_PENDING => 'bg-amber-100 text-amber-800 border border-amber-200 animate-pulse',
            self::STATUS_REJECTED => 'bg-rose-100 text-rose-800 border border-rose-200',
            self::STATUS_APPROVED => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    public function getStatusIcon(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT   => 'fa-pen-to-square',
            self::STATUS_PENDING => 'fa-clock',
            self::STATUS_REJECTED => 'fa-circle-xmark',
            self::STATUS_APPROVED => 'fa-circle-check',
            default => 'fa-circle-info',
        };
    }

    // =========================================================
    // Permission Checks
    // =========================================================

    public function canBeViewedBy(?User $user): bool
    {
        if (!$user) return false;
        if ($user->isSuperAdmin()) return true;
        if ($this->isApproved()) return true;
        if ($user->isAdmin() && $this->created_by === $user->id) return true;
        return false;
    }

    public function canBeEditedBy(?User $user): bool
    {
        if (!$user) return false;
        if ($user->isSuperAdmin()) return true;
        if ($user->isAdmin() && $this->created_by === $user->id) {
            return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED]);
        }
        return false;
    }

    public function canBeDeletedBy(?User $user): bool
    {
        if (!$user) return false;
        if ($user->isSuperAdmin()) return true;
        if ($user->isAdmin() && $this->created_by === $user->id && $this->isDraft()) {
            return true;
        }
        return false;
    }

    public function canBeApprovedBy(?User $user): bool
    {
        if (!$user) return false;
        return $user->isSuperAdmin() && $this->isPending();
    }

    public function canBeRejectedBy(?User $user): bool
    {
        if (!$user) return false;
        return $user->isSuperAdmin() && $this->isPending();
    }

    /**
     * Create snapshot into sop_versions
     */
    public function createSnapshot(User $user, ?string $description = null): SopVersion
    {
        $this->loadMissing(['revisionNotes', 'attachments', 'department.bod']);

        $snapshotData = [
            'nama_sop' => $this->nama_sop,
            'nomor_dokumen' => $this->nomor_dokumen,
            'department_id' => $this->department_id,
            'department_name' => $this->department?->name,
            'bod_id' => $this->bod_id,
            'bod_name' => $this->bod?->name,
            'tanggal_efektif' => $this->tanggal_efektif?->format('Y-m-d'),
            'tujuan' => $this->tujuan,
            'ruang_lingkup' => $this->ruang_lingkup,
            'definisi' => $this->definisi,
            'aturan_umum' => $this->aturan_umum,
            'status' => $this->status,
            'version' => $this->version,
            'revision_notes' => $this->revisionNotes->map(fn($n) => [
                'nomor_revisi' => $n->nomor_revisi,
                'tanggal_revisi' => $n->tanggal_revisi?->format('Y-m-d') ?? (string)$n->tanggal_revisi,
                'keterangan' => $n->keterangan,
            ])->toArray(),
            'attachments' => $this->attachments->map(fn($a) => [
                'jenis_file' => $a->jenis_file,
                'nama_file' => $a->nama_file,
                'file_path' => $a->file_path,
                'file_size' => $a->file_size,
            ])->toArray(),
        ];

        return $this->versions()->create([
            'version_number' => $this->version ?? '1.0',
            'data_snapshot' => $snapshotData,
            'changed_by' => $user->id,
            'change_description' => $description,
        ]);
    }
}

