<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SopVersion extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'sop_id',
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

    public function sop()
    {
        return $this->belongsTo(Sop::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}

