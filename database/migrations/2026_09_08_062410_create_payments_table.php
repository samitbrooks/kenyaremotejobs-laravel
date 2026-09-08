<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Every purchase in the app — credit packages, subscriptions, employer
     * job postings — routes through one row here regardless of what gateway
     * settles it. Today `gateway` is always 'mock' and `initiate()` marks
     * the row completed immediately (see App\Payments\MockGateway); wiring
     * M-Pesa Daraja later means adding a DarajaGateway that instead sends an
     * STK push, stores its CheckoutRequestID in `external_reference`, and
     * leaves the row pending until Safaricom's callback confirms it — no
     * schema change needed for that.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('gateway');
            $table->string('purpose');
            $table->json('payload');
            $table->unsignedInteger('amount_kes');
            $table->string('phone')->nullable();
            $table->string('status')->default('pending');
            $table->string('external_reference')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
