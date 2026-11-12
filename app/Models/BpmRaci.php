<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BpmRaci extends Model
{
    use HasFactory;

    protected $table = 'bpm_raci';

    protected $fillable = [
        'bpm_step_id',
        'responsibility_type',
        'position_id',
    ];

    public function step()
    {
        return $this->belongsTo(BpmStep::class, 'bpm_step_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}

