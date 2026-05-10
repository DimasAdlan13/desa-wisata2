<?php

namespace App\Livewire;

use App\Models\Service;
use App\Services\BookingService;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class BookingForm extends Component
{
    public Service $service;

    // Static fields
    public string $bookingDate = '';
    public int    $pax         = 1;
    public string $phone       = '';

    // Dynamic fields from form_schema
    public array  $dynamicFields = [];

    public ?int $participant_count = null;

    public function mount(Service $service): void
    {
        abort_if(!auth()->user()->isWisatawan(), 403, 'Hanya Wisatawan yang dapat membuat pesanan.');
        $this->service = $service;

        // Pre-fill phone dari profil user
        $this->phone = auth()->user()->phone ?? '';

        if ($this->service->pricing_type === 'per_unit') {
            $this->participant_count = 1;
        }

        // Initialize dynamic fields from form_schema (Repeater array of objects)
        if ($service->form_schema && is_array($service->form_schema)) {
            foreach ($service->form_schema as $field) {
                if (isset($field['pertanyaan'])) {
                    $key = \Illuminate\Support\Str::slug($field['pertanyaan'], '_');
                    $this->dynamicFields[$key] = '';
                }
            }
        }
    }

    protected function rules(): array
    {
        $rules = [
            'bookingDate' => ['required', 'date', 'after_or_equal:today'],
            'pax'         => ['required', 'integer', 'min:1', 'max:' . $this->service->quota_per_day],
            'phone'       => ['required', 'string', 'max:20'],
        ];

        if ($this->service->pricing_type === 'per_unit') {
            $rules['participant_count'] = ['required', 'integer', 'min:1'];
        }

        // Dynamic validation: all dynamic fields are required
        foreach ($this->dynamicFields as $key => $_) {
            $rules["dynamicFields.{$key}"] = ['required', 'string', 'max:500'];
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'bookingDate.required'        => 'Tanggal wisata wajib dipilih.',
            'bookingDate.after_or_equal'  => 'Tanggal wisata tidak boleh di masa lampau.',
            'pax.min'                     => 'Minimal 1.',
            'pax.max'                     => "Maksimal {$this->service->quota_per_day} {$this->service->unit_name} per hari.",
            'phone.required'              => 'Nomor WhatsApp wajib diisi agar admin dapat menghubungi Anda.',
            'participant_count.required'  => 'Jumlah peserta rombongan wajib diisi.',
            'participant_count.min'       => 'Minimal 1 orang.',
        ];
    }

    /**
     * Livewire lifecycle: re-check availability when booking date changes.
     */
    public function updatedBookingDate(string $value): void
    {
        $this->validateOnly('bookingDate');
    }

    /**
     * Get remaining quota for the selected date (reactive).
     */
    public function getRemainingQuotaProperty(): ?int
    {
        if (empty($this->bookingDate)) {
            return null;
        }
        return (new BookingService())->getRemainingQuota($this->service, $this->bookingDate);
    }

    public function submit(): void
    {
        // Pasang Satpam: Cegah spam booking (Maksimal 3 booking per menit)
        $throttleKey = 'booking|' . auth()->id() . '|' . request()->ip();
        
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            $this->addError('bookingDate', "Terlalu banyak permintaan pemesanan. Tunggu $seconds detik.");
            return;
        }

        // Catat setiap percobaan pemesanan SEBELUM validasi
        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);

        $this->validate();

        try {
            $details = array_merge(
                $this->dynamicFields,
                ['nomor_wa_pemesan' => $this->phone]
            );

            if ($this->service->pricing_type === 'per_unit' && $this->participant_count) {
                $details['jumlah_peserta_rombongan'] = $this->participant_count . ' Orang';
            }

            $booking = (new BookingService())->createBooking(
                auth()->user(),
                $this->service,
                [
                    'booking_date'    => $this->bookingDate,
                    'pax'             => $this->pax,
                    'booking_details' => $details,
                ]
            );

            session()->flash('success', "Booking berhasil! Kode booking Anda: {$booking->booking_code}");
            $this->redirect(route('dashboard.booking', $booking), navigate: true);

        } catch (ValidationException $e) {
            $this->addError('bookingDate', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.booking-form')
            ->layout('layouts.app');
    }
}
