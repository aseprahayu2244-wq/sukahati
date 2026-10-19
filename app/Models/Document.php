<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'bod_id', 'division_id', 'position_id', 'title', 'description',
        'file_path', 'file_name', 'file_type', 'file_extension', 'file_size', 'uploaded_by'
    ];

    // Type labels (new menu names)
    public static array $typeLabels = [
        'sop'      => 'SOP',
        'job_desk' => 'Job Desk',
        'bpm'      => 'BPM',
        'struktur' => 'Struktur',
    ];

    // Type colors
    public static array $typeColors = [
        'sop'      => '#3b82f6',
        'job_desk' => '#10b981',
        'bpm'      => '#f59e0b',
        'struktur' => '#8b5cf6',
    ];

    // Type icons
    public static array $typeIcons = [
        'sop'      => 'fa-file-contract',
        'job_desk' => 'fa-user-gear',
        'bpm'      => 'fa-diagram-project',
        'struktur' => 'fa-sitemap',
    ];

    public function bod()
    {
        return $this->belongsTo(Bod::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getTypeLabel(): string
    {
        return self::$typeLabels[$this->type] ?? strtoupper($this->type);
    }

    public function getFileSizeFormatted(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' bytes';
    }

    public function isPreviewable(): bool
    {
        $previewable = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'mp4', 'webm', 'mp3', 'wav'];
        return in_array(strtolower($this->file_extension), $previewable);
    }

    public function getFileIconClass(): string
    {
        $ext = strtolower($this->file_extension ?? pathinfo($this->file_name ?? '', PATHINFO_EXTENSION));
        $mime = strtolower($this->file_type ?? '');

        if ($ext === 'pdf' || str_contains($mime, 'pdf')) {
            return 'fa-solid fa-file-pdf';
        }
        if (in_array($ext, ['doc', 'docx']) || str_contains($mime, 'word') || str_contains($mime, 'officedocument.wordprocessingml')) {
            return 'fa-solid fa-file-word';
        }
        if (in_array($ext, ['xls', 'xlsx', 'csv']) || str_contains($mime, 'excel') || str_contains($mime, 'spreadsheetml') || str_contains($mime, 'csv')) {
            return 'fa-solid fa-file-excel';
        }
        if (in_array($ext, ['ppt', 'pptx']) || str_contains($mime, 'powerpoint') || str_contains($mime, 'presentationml')) {
            return 'fa-solid fa-file-powerpoint';
        }
        if (in_array($ext, ['mp4', 'mkv', 'avi', 'mov', 'webm']) || str_contains($mime, 'video')) {
            return 'fa-solid fa-file-video';
        }
        if (in_array($ext, ['mp3', 'wav', 'ogg', 'm4a']) || str_contains($mime, 'audio')) {
            return 'fa-solid fa-file-audio';
        }
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']) || str_contains($mime, 'image')) {
            return 'fa-solid fa-file-image';
        }
        if (in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz']) || str_contains($mime, 'zip') || str_contains($mime, 'compressed')) {
            return 'fa-solid fa-file-zipper';
        }
        if (in_array($ext, ['txt', 'rtf']) || str_contains($mime, 'text')) {
            return 'fa-solid fa-file-lines';
        }
        return 'fa-solid fa-file';
    }

    public function getFileColorClass(): string
    {
        $ext = strtolower($this->file_extension ?? pathinfo($this->file_name ?? '', PATHINFO_EXTENSION));
        $mime = strtolower($this->file_type ?? '');

        if ($ext === 'pdf' || str_contains($mime, 'pdf')) {
            return 'bg-red-50 text-red-600 border border-red-200';
        }
        if (in_array($ext, ['doc', 'docx']) || str_contains($mime, 'word') || str_contains($mime, 'officedocument.wordprocessingml')) {
            return 'bg-blue-50 text-blue-600 border border-blue-200';
        }
        if (in_array($ext, ['xls', 'xlsx', 'csv']) || str_contains($mime, 'excel') || str_contains($mime, 'spreadsheetml') || str_contains($mime, 'csv')) {
            return 'bg-emerald-50 text-emerald-600 border border-emerald-200';
        }
        if (in_array($ext, ['ppt', 'pptx']) || str_contains($mime, 'powerpoint') || str_contains($mime, 'presentationml')) {
            return 'bg-amber-50 text-amber-600 border border-amber-200';
        }
        if (in_array($ext, ['mp4', 'mkv', 'avi', 'mov', 'webm']) || str_contains($mime, 'video')) {
            return 'bg-purple-50 text-purple-600 border border-purple-200';
        }
        if (in_array($ext, ['mp3', 'wav', 'ogg', 'm4a']) || str_contains($mime, 'audio')) {
            return 'bg-cyan-50 text-cyan-600 border border-cyan-200';
        }
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']) || str_contains($mime, 'image')) {
            return 'bg-rose-50 text-rose-600 border border-rose-200';
        }
        if (in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'])) {
            return 'bg-slate-100 text-slate-700 border border-slate-300';
        }
        return 'bg-slate-50 text-slate-600 border border-slate-200';
    }

    public function getFileBadgeLabel(): string
    {
        $ext = strtolower($this->file_extension ?? pathinfo($this->file_name ?? '', PATHINFO_EXTENSION));
        return strtoupper($ext ?: 'FILE');
    }

    public function getFileIcon(): string
    {
        return match(strtolower($this->file_extension ?? '')) {
            'pdf'                 => '📄',
            'doc', 'docx'        => '📝',
            'xls', 'xlsx'        => '📊',
            'ppt', 'pptx'        => '📑',
            'jpg', 'jpeg', 'png',
            'gif', 'webp', 'svg' => '🖼️',
            'mp4', 'avi', 'mov',
            'webm'               => '🎬',
            'mp3', 'wav'         => '🎵',
            'zip', 'rar', '7z'   => '📦',
            default              => '📁',
        };
    }

    public function getUrl(): string
    {
        return Storage::url($this->file_path);
    }
}
