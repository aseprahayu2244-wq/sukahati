<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SopAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sop_id',
        'jenis_file',
        'nama_file',
        'file_path',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    public function sop()
    {
        return $this->belongsTo(Sop::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function isFlowchart(): bool
    {
        return $this->jenis_file === 'flowchart';
    }

    public function isImage(): bool
    {
        return in_array(strtolower(pathinfo($this->nama_file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'])
            || str_starts_with($this->mime_type ?? '', 'image/');
    }

    public function isPdf(): bool
    {
        return strtolower(pathinfo($this->nama_file, PATHINFO_EXTENSION)) === 'pdf'
            || ($this->mime_type === 'application/pdf');
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    public function getFileIconClass(): string
    {
        $ext = strtolower(pathinfo($this->nama_file, PATHINFO_EXTENSION));
        return match($ext) {
            'pdf' => 'fa-file-pdf text-rose-500',
            'doc', 'docx' => 'fa-file-word text-blue-500',
            'xls', 'xlsx', 'csv' => 'fa-file-excel text-emerald-500',
            'ppt', 'pptx' => 'fa-file-powerpoint text-amber-500',
            'png', 'jpg', 'jpeg', 'webp' => 'fa-file-image text-purple-500',
            'zip', 'rar', '7z' => 'fa-file-zipper text-amber-600',
            default => 'fa-file text-slate-400',
        };
    }

    public function getUrl(): string
    {
        return route('sops.attachments.download', [$this->sop_id, $this->id]);
    }

    public function getPreviewUrl(): string
    {
        return route('sops.attachments.preview', [$this->sop_id, $this->id]);
    }
}

