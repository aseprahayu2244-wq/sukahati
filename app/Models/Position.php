<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = ['division_id', 'name', 'description', 'parent_id', 'level', 'order'];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function parent()
    {
        return $this->belongsTo(Position::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Position::class, 'parent_id')->orderBy('order');
    }

    public function getDocumentsByType(string $type)
    {
        return $this->documents()->where('type', $type)->get();
    }
}
