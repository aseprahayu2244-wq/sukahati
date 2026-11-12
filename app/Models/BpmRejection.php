<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BpmRejection extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'bpm_rejections';

    protected $fillable = [
        'bpm_id',
        'reviewer_id',
        'rejection_reason',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function bpm()
    {
        return $this->belongsTo(Bpm::class, 'bpm_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}

