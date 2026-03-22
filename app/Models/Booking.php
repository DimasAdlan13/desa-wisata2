<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_code', 'user_id', 'service_id', 'booking_date', 'pax',
        'total_price', 'booking_details', 'status', 'rejection_reason',
        'payment_proof', 'payment_confirmed_at', 'payment_confirmed_by',
    ];

    protected $casts = [
        'booking_details'     => 'array',
        'booking_date'        => 'date',
        'payment_confirmed_at'=> 'datetime',
        'total_price'         => 'integer',
        'pax'                 => 'integer',
    ];

    // ─── Status Constants ───────────────────────────────────────────
    const STATUS_PENDING   = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REJECTED  = 'rejected';

    // ─── Auto-generate booking_code on create ────────────────────────
    protected static function booted(): void
    {
        static::creating(function (self $booking) {
            if (empty($booking->booking_code)) {
                $booking->booking_code = self::generateCode();
            }
        });
    }

    public static function generateCode(): string
    {
        $date = now()->format('Ymd');
        // Count today's bookings and pad
        $count = static::whereDate('created_at', today())->withTrashed()->count() + 1;
        return 'DW-' . $date . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    // ─── Relationships ──────────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function rating(): HasOne
    {
        return $this->hasOne(Rating::class);
    }

    public function paymentConfirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_confirmed_by');
    }

    // ─── Helpers ─────────────────────────────────────────────────────
    /**
     * Can the user submit a rating for this booking?
     * Conditions: status=completed AND no existing rating.
     */
    public function isRateable(): bool
    {
        return $this->status === self::STATUS_COMPLETED && !$this->rating()->exists();
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    // ─── Accessors ──────────────────────────────────────────────────
    public function getFormattedTotalPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING   => 'warning',
            self::STATUS_CONFIRMED => 'info',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLED => 'gray',
            self::STATUS_REJECTED  => 'danger',
            default                => 'gray',
        };
    }

    // ─── Scopes ─────────────────────────────────────────────────────
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }
}
