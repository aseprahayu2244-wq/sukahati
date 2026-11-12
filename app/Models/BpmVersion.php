<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BpmVersion extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'bpm_versions';

    protected $fillable = [
        'bpm_id',
        'version_number',
        'data_snapshot',
        'changed_by',
        'change_description',
        'created_at',
    ];

    protected $casts = [
        'data_snapshot' => 'array',
        'created_at' => 'datetime',
    ];

    public function bpm()
    {
        return $this->belongsTo(Bpm::class, 'bpm_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}

