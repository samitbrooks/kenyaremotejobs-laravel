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
        // A purchase grants N credits for one tier; spending one is just
        // recording a job_unlocks row for a job of that tier. Remaining
        // balance is never stored directly — always derived as
        // SUM(credits here) - COUNT(job_unlocks for that tier), the same
        // ledger-not-counter pattern used throughout, so a balance can never
        // drift out of sync with what was actually purchased and spent.
        Schema::create('credit_purchases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('tier');
            $table->unsignedInteger('credits');
            $table->unsignedInteger('amount_kes');
            $table->timestamp('purchased_at');

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_purchases');
    }
};
