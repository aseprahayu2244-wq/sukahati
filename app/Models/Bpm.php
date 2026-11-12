<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bpm extends Model
{
    use HasFactory;

    protected $table = 'bpms';

    // Status Constants
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'menunggu_persetujuan';
    public const STATUS_REJECTED = 'ditolak';
    public const STATUS_APPROVED = 'disetujui';

    // Source Type Constants
    public const SOURCE_BUILDER = 'builder';
    public const SOURCE_UPLOAD = 'upload';

    protected $fillable = [
        'nama_bpm',
        'nomor_dokumen',
        'bod_id',
        'department_id',
        'tanggal_efektif',
        'deskripsi',
        'source_type',
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

    // Relationships
    public function bod()
    {
        return $this->belongsTo(Bod::class, 'bod_id');
    }

    public function department()
    {
        return $this->belongsTo(Division::class, 'department_id');
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

    public function steps()
    {
        return $this->hasMany(BpmStep::class, 'bpm_id')->orderBy('sort_order');
    }

    public function attachments()
    {
        return $this->hasMany(BpmAttachment::class, 'bpm_id');
    }

    public function rejections()
    {
        return $this->hasMany(BpmRejection::class, 'bpm_id')->latest('created_at');
    }

    public function latestRejection()
    {
        return $this->hasOne(BpmRejection::class, 'bpm_id')->latestOfMany('created_at');
    }

    public function versions()
    {
        return $this->hasMany(BpmVersion::class, 'bpm_id')->latest('created_at');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'bpm_id');
    }

    // Status helpers
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

    public function isBuilder(): bool
    {
        return $this->source_type === self::SOURCE_BUILDER;
    }

    public function isUpload(): bool
    {
        return $this->source_type === self::SOURCE_UPLOAD;
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT     => 'Draft',
            self::STATUS_PENDING   => 'Menunggu Persetujuan',
            self::STATUS_REJECTED  => 'Ditolak',
            self::STATUS_APPROVED  => 'Disetujui',
            default                => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT     => 'bg-slate-100 text-slate-700 border-slate-200',
            self::STATUS_PENDING   => 'bg-amber-100 text-amber-800 border-amber-200',
            self::STATUS_REJECTED  => 'bg-rose-100 text-rose-800 border-rose-200',
            self::STATUS_APPROVED  => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            default                => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public function getStatusIcon(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT     => 'fa-pen-ruler',
            self::STATUS_PENDING   => 'fa-clock',
            self::STATUS_REJECTED  => 'fa-circle-xmark',
            self::STATUS_APPROVED  => 'fa-circle-check',
            default                => 'fa-circle-info',
        };
    }

    // Authorization checks
    public function canBeViewedBy(?User $user): bool
    {
        if (!$user) return false;
        if ($user->isSuperAdmin()) return true;
        if ($this->isApproved()) return true; // All authenticated roles can see approved
        // Creator Admin can see their own drafts, pending, and rejected
        return $user->isAdmin() && $this->created_by === $user->id;
    }

    public function canBeEditedBy(?User $user): bool
    {
        if (!$user) return false;
        if ($user->isSuperAdmin()) return true; // Super admin can edit any
        // Admin can only edit own draft or rejected
        return $user->isAdmin() && $this->created_by === $user->id && ($this->isDraft() || $this->isRejected());
    }

    public function canBeDeletedBy(?User $user): bool
    {
        if (!$user) return false;
        if ($user->isSuperAdmin()) return true;
        return $user->isAdmin() && $this->created_by === $user->id && $this->isDraft();
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
     * Create snapshot for versioning before changes
     */
    public function createSnapshot(User $user, ?string $changeDescription = null): BpmVersion
    {
        $this->loadMissing(['steps.raci.position', 'attachments']);

        $stepsData = $this->steps->map(function ($step) {
            return [
                'id' => $step->id,
                'level' => $step->level,
                'activity_description' => $step->activity_description,
                'sort_order' => $step->sort_order,
                'parent_id' => $step->parent_id,
                'raci' => $step->raci->map(fn($r) => [
                    'responsibility_type' => $r->responsibility_type,
                    'position_id' => $r->position_id,
                    'position_name' => $r->position?->name,
                ])->toArray(),
            ];
        })->toArray();

        $attachmentsData = $this->attachments->map(fn($att) => [
            'id' => $att->id,
            'file_name' => $att->file_name,
            'file_path' => $att->file_path,
            'mime_type' => $att->mime_type,
            'file_size' => $att->file_size,
        ])->toArray();

        $snapshot = [
            'nama_bpm' => $this->nama_bpm,
            'nomor_dokumen' => $this->nomor_dokumen,
            'bod_id' => $this->bod_id,
            'department_id' => $this->department_id,
            'tanggal_efektif' => $this->tanggal_efektif?->format('Y-m-d'),
            'deskripsi' => $this->deskripsi,
            'source_type' => $this->source_type,
            'version' => $this->version,
            'steps' => $stepsData,
            'attachments' => $attachmentsData,
        ];

        return BpmVersion::create([
            'bpm_id' => $this->id,
            'version_number' => $this->version,
            'data_snapshot' => $snapshot,
            'changed_by' => $user->id,
            'change_description' => $changeDescription,
            'created_at' => now(),
        ]);
    }
}

