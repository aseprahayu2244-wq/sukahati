<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    // Division acts as Department in the new system
    protected $fillable = ['bod_id', 'name', 'description', 'color', 'order'];

    public function bod()
    {
        return $this->belongsTo(Bod::class);
    }

    public function positions()
    {
        return $this->hasMany(Position::class)->orderBy('order');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function getDocumentCountByType(string $type): int
    {
        return $this->documents()->where('type', $type)->count();
    }
}
