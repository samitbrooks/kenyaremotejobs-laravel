<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // A paid job posting from the employer platform. amount_kes is
        // recorded at post time so a later plan-price change never rewrites
        // history — same pattern as job_unlocks/credit_purchases.
        Schema::create('job_posting_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('job_listing_id');
            $table->foreign('job_listing_id')->references('id')->on('job_listings')->cascadeOnDelete();
            $table->string('plan');
            $table->unsignedInteger('amount_kes');
            $table->timestamp('posted_at');

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_posting_payments');
    }
};
