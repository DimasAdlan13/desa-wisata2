<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id', 'photo_path', 'is_primary', 'order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'order'      => 'integer',
    ];

    // ─── Relationships ──────────────────────────────────────────────
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
