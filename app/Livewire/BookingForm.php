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

    public function mount(Service $service): void
    {
        abort_if(!auth()->user()->isWisatawan(), 403, 'Hanya Wisatawan yang dapat membuat pesanan.');
        $this->service = $service;

        // Pre-fill phone dari profil user
        $this->phone = auth()->user()->phone ?? '';

        // Initialize dynamic fields from form_schema
        if ($service->form_schema) {
            foreach ($service->form_schema as $key => $label) {
                $this->dynamicFields[$key] = '';
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
            'pax.min'                     => 'Minimal 1 orang.',
            'pax.max'                     => "Maksimal {$this->service->quota_per_day} orang per hari.",
            'phone.required'              => 'Nomor WhatsApp wajib diisi agar admin dapat menghubungi Anda.',
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
        $this->validate();

        try {
            $booking = (new BookingService())->createBooking(
                auth()->user(),
                $this->service,
                [
                    'booking_date'    => $this->bookingDate,
                    'pax'             => $this->pax,
                    'booking_details' => array_merge(
                        $this->dynamicFields,
                        ['nomor_wa_pemesan' => $this->phone]
                    ),
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
