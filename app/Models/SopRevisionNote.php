<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SopRevisionNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'sop_id',
        'nomor_revisi',
        'tanggal_revisi',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_revisi' => 'date',
    ];

    public function sop()
    {
        return $this->belongsTo(Sop::class);
    }
}

