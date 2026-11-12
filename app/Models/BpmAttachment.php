<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BpmAttachment extends Model
{
    use HasFactory;

    protected $table = 'bpm_attachments';

    protected $fillable = [
        'bpm_id',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    public function bpm()
    {
        return $this->belongsTo(Bpm::class, 'bpm_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function isPdf(): bool
    {
        return str_contains($this->mime_type ?? '', 'pdf') || str_ends_with(strtolower($this->file_path), '.pdf');
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'image/') ||
            preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $this->file_path);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}

