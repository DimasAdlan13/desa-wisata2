<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Content extends Model
{
    use HasFactory, SoftDeletes;

    // Content type constants
    const TYPE_UMKM       = 'umkm';
    const TYPE_KULINER     = 'kuliner';
    const TYPE_INFO_WISATA = 'info_wisata';

    protected $fillable = [
        'title', 'slug', 'body', 'type', 'cover_image',
        'is_published', 'published_at', 'is_featured',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured'  => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $content) {
            if (empty($content->slug)) {
                $content->slug = Str::slug($content->title) . '-' . Str::random(5);
            }
        });
    }

    // ─── Scopes ─────────────────────────────────────────────────────
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_UMKM       => 'UMKM',
            self::TYPE_KULINER    => 'Kuliner',
            self::TYPE_INFO_WISATA=> 'Info Wisata',
            default               => $this->type,
        };
    }
}
