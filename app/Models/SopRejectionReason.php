<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SopRejectionReason extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'sop_id',
        'reviewer_id',
        'alasan_penolakan',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function sop()
    {
        return $this->belongsTo(Sop::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}

