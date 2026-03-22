<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BookingService
{
    /**
     * Check if the service has enough quota for the given date and pax.
     *
     * @param Service $service
     * @param string  $date      Y-m-d
     * @param int     $pax       number of persons
     * @return bool
     */
    public function checkAvailability(Service $service, string $date, int $pax = 1): bool
    {
        $bookedPax = Booking::where('service_id', $service->id)
            ->where('booking_date', $date)
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->sum('pax');

        return ($bookedPax + $pax) <= $service->quota_per_day;
    }

    /**
     * Get remaining quota for a service on a given date.
     */
    public function getRemainingQuota(Service $service, string $date): int
    {
        $bookedPax = Booking::where('service_id', $service->id)
            ->where('booking_date', $date)
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->sum('pax');

        return max(0, $service->quota_per_day - $bookedPax);
    }

    /**
     * Create a new booking after checking availability.
     *
     * @param User    $user
     * @param Service $service
     * @param array   $data  [booking_date, pax, booking_details]
     * @return Booking
     * @throws ValidationException
     */
    public function createBooking(User $user, Service $service, array $data): Booking
    {
        $pax  = $data['pax'] ?? 1;
        $date = $data['booking_date'];

        if (!$this->checkAvailability($service, $date, $pax)) {
            throw ValidationException::withMessages([
                'booking_date' => "Maaf, kuota layanan '{$service->name}' pada tanggal {$date} sudah penuh.",
            ]);
        }

        return DB::transaction(function () use ($user, $service, $data, $pax) {
            $booking = Booking::create([
                'user_id'         => $user->id,
                'service_id'      => $service->id,
                'booking_date'    => $data['booking_date'],
                'pax'             => $pax,
                'total_price'     => $service->price * $pax,
                'booking_details' => $data['booking_details'] ?? [],
                'status'          => Booking::STATUS_PENDING,
            ]);

            if ($service->user) {
                $service->user->notify(new \App\Notifications\AdminBookingNotification($booking));
            }

            return $booking;
        });
    }

    /**
     * Upload payment proof and attach to booking.
     */
    public function uploadPaymentProof(Booking $booking, $file): Booking
    {
        if ($booking->payment_proof) {
            Storage::disk('public')->delete($booking->payment_proof);
        }

        $path = $file->store('payment-proofs', 'public');
        $booking->update(['payment_proof' => $path]);

        return $booking->fresh();
    }

    /**
     * Admin confirms payment and changes status to confirmed.
     */
    public function confirmPayment(Booking $booking, User $confirmedBy): Booking
    {
        $booking->update([
            'status'               => Booking::STATUS_CONFIRMED,
            'payment_confirmed_at' => now(),
            'payment_confirmed_by' => $confirmedBy->id,
        ]);

        return $booking->fresh();
    }

    /**
     * Mark a booking as completed.
     */
    public function completeBooking(Booking $booking): Booking
    {
        $booking->update(['status' => Booking::STATUS_COMPLETED]);
        return $booking->fresh();
    }

    /**
     * Cancel a booking (by user or admin).
     */
    public function cancelBooking(Booking $booking, ?string $reason = null): Booking
    {
        $booking->update([
            'status'           => Booking::STATUS_CANCELLED,
            'rejection_reason' => $reason,
        ]);

        return $booking->fresh();
    }

    /**
     * Reject a booking with reason (by admin).
     */
    public function rejectBooking(Booking $booking, string $reason): Booking
    {
        $booking->update([
            'status'           => Booking::STATUS_REJECTED,
            'rejection_reason' => $reason,
        ]);

        return $booking->fresh();
    }
}
