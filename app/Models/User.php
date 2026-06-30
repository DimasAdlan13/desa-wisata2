<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    // Role constants
    const ROLE_SUPER_ADMIN    = 'super_admin';
    const ROLE_ADMIN_LAYANAN  = 'admin_layanan';
    const ROLE_WISATAWAN      = 'wisatawan';

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'role',
        'province', 'city',
        'business_name', 'business_address', 'business_description', 'business_photo',
        'is_approved', 'approved_by', 'approved_at',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_approved'       => 'boolean',
        'approved_at'       => 'datetime',
    ];

    // ─── Events ────────────────────────────────────────────────────
    protected static function booted(): void
    {
        static::creating(function (User $user) {
            $currentUser = auth()->user();
            
            // 1. Jika yang membuat akun adalah Super Admin dari panel, langsung setujui apapun rolenya
            if ($currentUser && $currentUser->isSuperAdmin()) {
                $user->is_approved = true;
                $user->approved_by = $currentUser->id;
                $user->approved_at = now();
            } 
            // 2. Jika bukan dibuat Super Admin (misal dari form register), Wisatawan & Super Admin tetap otomatis disetujui
            elseif (in_array($user->role, [self::ROLE_SUPER_ADMIN, self::ROLE_WISATAWAN])) {
                $user->is_approved = true;
            }
        });
    }

    // ─── Role Helpers ──────────────────────────────────────────────
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isAdminLayanan(): bool
    {
        return $this->role === self::ROLE_ADMIN_LAYANAN;
    }

    public function isWisatawan(): bool
    {
        return $this->role === self::ROLE_WISATAWAN;
    }

    /**
     * Wisatawan & super_admin are considered "approved" by default.
     * Only admin_layanan needs manual approval.
     */
    public function isActive(): bool
    {
        if ($this->role !== self::ROLE_ADMIN_LAYANAN) {
            return true;
        }
        return $this->is_approved;
    }

    // ─── Relationships ──────────────────────────────────────────────
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    // ─── Scopes ─────────────────────────────────────────────────────
    public function scopeAdminLayanan($query)
    {
        return $query->where('role', self::ROLE_ADMIN_LAYANAN);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('role', self::ROLE_ADMIN_LAYANAN)->where('is_approved', false);
    }

    // ─── Filament Panel Access ───────────────────────────────────────
    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN_LAYANAN])
            && $this->isActive();
    }
}
