<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'phone',
        'jabatan',
        'avatar',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Role check helpers
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAnggota(): bool
    {
        return $this->role === 'anggota';
    }

    public function canManageDocuments(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function canManageBod(): bool
    {
        return $this->role === 'super_admin';
    }

    public function canManageDepartment(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function canManageUsers(): bool
    {
        return $this->role === 'super_admin';
    }

    public function getRoleLabel(): string
    {
        return match($this->role) {
            'super_admin' => 'Super Admin',
            'admin'       => 'Admin',
            'anggota'     => 'Anggota',
            default       => ucfirst($this->role),
        };
    }

    public function getRoleBadgeClass(): string
    {
        return match($this->role) {
            'super_admin' => 'bg-red-100 text-red-700 border border-red-200',
            'admin'       => 'bg-amber-100 text-amber-700 border border-amber-200',
            'anggota'     => 'bg-slate-100 text-slate-700 border border-slate-200',
            default       => 'bg-slate-100 text-slate-700',
        };
    }

    public function getAvatarUrl(): string
    {
        if ($this->avatar) {
            return \Storage::url($this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=3b82f6&color=fff&size=128';
    }

    public function uploadedDocuments()
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    // Backward compat aliases (used in old views)
    public function isKetua(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }

    public function isUserUmum(): bool
    {
        return $this->isAnggota();
    }
}
