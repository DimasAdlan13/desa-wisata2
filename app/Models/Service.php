<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'pricing_type',
        'unit_name',
        'quota_per_day',
        'location',
        'contact_person',
        'form_schema',
        'is_approved',
        'approved_by',
        'approved_at',
        'is_active',
    ];

    protected $casts = [
        'form_schema' => 'array',
        'is_approved' => 'boolean',
        'is_active' => 'boolean',
        'approved_at' => 'datetime',
        'price' => 'integer',
        'quota_per_day' => 'integer',
    ];

    // Auto-generate slug from name
    protected static function booted(): void
    {
        static::creating(function (self $service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name) . '-' . Str::random(5);
            }
        });
    }

    // ─── Relationships ──────────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ServicePhoto::class)->orderBy('order');
    }

    public function primaryPhoto()
    {
        return $this->hasOne(ServicePhoto::class)->where('is_primary', true);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ─── Scopes ─────────────────────────────────────────────────────
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->approved()->active();
    }

    // ─── Accessors ──────────────────────────────────────────────────
    public function getAverageRatingAttribute(): float
    {
        return round($this->ratings()->avg('rating') ?? 0, 1);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
