<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'divisi',
        'nim',
        'phone',
        'avatar',
        'signature',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function faceData(): HasOne
    {
        return $this->hasOne(FaceData::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function activityReports(): HasMany
    {
        return $this->hasMany(ActivityReport::class);
    }

    public function approvedAttendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'approved_by');
    }

    public function isDpl(): bool
    {
        return $this->role === 'dpl';
    }

    public function isKoordinator(): bool
    {
        return $this->role === 'koordinator';
    }

    public function isAnggota(): bool
    {
        return $this->role === 'anggota';
    }

    public function isSekretaris(): bool
    {
        return $this->role === 'sekretaris';
    }

    /**
     * Scope query to include all 17 KKN members (Anggota, Sekretaris, and Koordinator student).
     */
    public function scopeMembers($query)
    {
        return $query->whereIn('role', ['anggota', 'sekretaris', 'koordinator'])
                     ->where('name', '!=', 'Koordinator KKN');
    }
}
