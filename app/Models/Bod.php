<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bod extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'color', 'order'];

    public function departments()
    {
        return $this->hasMany(Division::class, 'bod_id')->orderBy('order');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'bod_id');
    }

    public function getDocumentCountByType(string $type): int
    {
        return $this->documents()->where('type', $type)->count();
    }
}
