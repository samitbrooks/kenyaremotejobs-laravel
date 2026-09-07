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
        // One paid unlock per (user, job) — the tier/amount are recorded at
        // purchase time so a later tier-price change never rewrites history.
        Schema::create('job_unlocks', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('job_listing_id');
            $table->foreign('job_listing_id')->references('id')->on('job_listings')->cascadeOnDelete();
            $table->string('tier');
            $table->unsignedInteger('amount_kes');
            $table->timestamp('unlocked_at');

            $table->primary(['user_id', 'job_listing_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_unlocks');
    }
};
