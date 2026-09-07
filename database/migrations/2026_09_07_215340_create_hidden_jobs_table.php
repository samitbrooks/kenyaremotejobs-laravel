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
        // An admin-hidden job listing id — sync (job_listings) only ever
        // touches rows it owns, so this list is what a resync consults to
        // avoid resurrecting something an admin explicitly took down.
        Schema::create('hidden_jobs', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->timestamp('hidden_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hidden_jobs');
    }
};
