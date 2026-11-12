<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'sop_id',
        'bpm_id',
        'title',
        'message',
        'type',
        'read_at',
        'created_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sop()
    {
        return $this->belongsTo(Sop::class);
    }

    public function bpm()
    {
        return $this->belongsTo(Bpm::class);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markAsRead(): void
    {
        if (!$this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function getIcon(): string
    {
        return match($this->type) {
            'sop_submission', 'bpm_submission' => 'fa-file-circle-plus text-blue-500',
            'sop_approved', 'bpm_approved'     => 'fa-circle-check text-emerald-500',
            'sop_rejected', 'bpm_rejected'     => 'fa-circle-xmark text-rose-500',
            'sop_resubmitted', 'bpm_resubmitted'=> 'fa-rotate-right text-amber-500',
            default                            => 'fa-bell text-slate-500',
        };
    }

    public function getBgClass(): string
    {
        return match($this->type) {
            'sop_submission', 'bpm_submission'   => 'bg-blue-50 border-blue-200',
            'sop_approved', 'bpm_approved'       => 'bg-emerald-50 border-emerald-200',
            'sop_rejected', 'bpm_rejected'       => 'bg-rose-50 border-rose-200',
            'sop_resubmitted', 'bpm_resubmitted' => 'bg-amber-50 border-amber-200',
            default                              => 'bg-slate-50 border-slate-200',
        };
    }

    /**
     * Send notification to a specific user
     */
    public static function send(int $userId, string $title, string $message, string $type, ?int $sopId = null, ?int $bpmId = null): self
    {
        return self::create([
            'user_id' => $userId,
            'sop_id' => $sopId,
            'bpm_id' => $bpmId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'created_at' => now(),
        ]);
    }

    /**
     * Broadcast notification to all active Super Admins
     */
    public static function notifySuperAdmins(string $title, string $message, string $type, ?int $sopId = null, ?int $bpmId = null): void
    {
        $superAdmins = User::where('role', 'super_admin')->where('is_active', true)->get();
        foreach ($superAdmins as $admin) {
            self::create([
                'user_id' => $admin->id,
                'sop_id' => $sopId,
                'bpm_id' => $bpmId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'created_at' => now(),
            ]);
        }
    }
}

