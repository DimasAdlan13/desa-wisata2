<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 30)->unique(); // e.g. DW-20240101-00001
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->date('booking_date');
            $table->unsignedSmallInteger('pax')->default(1); // number of persons
            $table->unsignedBigInteger('total_price');

            // Dynamic form data from service form_schema
            $table->json('booking_details')->nullable();

            // Booking status flow: pending → confirmed → completed | cancelled | rejected
            $table->enum('status', [
                'pending',
                'confirmed',
                'completed',
                'cancelled',
                'rejected',
            ])->default('pending');

            $table->text('rejection_reason')->nullable();

            // Payment
            $table->string('payment_proof')->nullable();
            $table->timestamp('payment_confirmed_at')->nullable();
            $table->foreignId('payment_confirmed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['service_id', 'booking_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
