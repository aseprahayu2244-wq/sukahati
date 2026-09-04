<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'division_id', 'position_id', 'title', 'description',
        'file_path', 'file_name', 'file_type', 'file_extension', 'file_size', 'uploaded_by'
    ];

    // Type labels
    public static array $typeLabels = [
        'sop'          => 'SOP',
        'job'          => 'Job',
        'job_analysis' => 'Job Analysis',
        'bpmn'         => 'BPMN',
    ];

    // Type colors
    public static array $typeColors = [
        'sop'          => '#3b82f6',
        'job'          => '#10b981',
        'job_analysis' => '#8b5cf6',
        'bpmn'         => '#f59e0b',
    ];

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
