<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BpmStep extends Model
{
    use HasFactory;

    protected $table = 'bpm_steps';

    protected $fillable = [
        'bpm_id',
        'level',
        'activity_description',
        'sort_order',
        'parent_id',
    ];

    public function bpm()
    {
        return $this->belongsTo(Bpm::class, 'bpm_id');
    }

    public function parent()
    {
        return $this->belongsTo(BpmStep::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(BpmStep::class, 'parent_id')->orderBy('sort_order');
    }

    public function raci()
    {
        return $this->hasMany(BpmRaci::class, 'bpm_step_id');
    }

    public function responsible()
    {
        return $this->hasMany(BpmRaci::class, 'bpm_step_id')->where('responsibility_type', 'R');
    }

    public function accountable()
    {
        return $this->hasMany(BpmRaci::class, 'bpm_step_id')->where('responsibility_type', 'A');
    }

    public function consulted()
    {
        return $this->hasMany(BpmRaci::class, 'bpm_step_id')->where('responsibility_type', 'C');
    }

    public function informed()
    {
        return $this->hasMany(BpmRaci::class, 'bpm_step_id')->where('responsibility_type', 'I');
    }

    /**
     * Helper to get indentation padding class for visual hierarchy
     */
    public function getIndentClass(): string
    {
        return match($this->level) {
            'L1' => 'pl-2',
            'L2' => 'pl-8',
            'L3' => 'pl-14',
            default => 'pl-2',
        };
    }

    /**
     * Helper to get badge styling based on level
     */
    public function getLevelBadgeClass(): string
    {
        return match($this->level) {
            'L1' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            'L2' => 'bg-sky-100 text-sky-800 border-sky-200',
            'L3' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            default => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }
}

