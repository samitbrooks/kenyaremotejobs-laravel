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
        // Minimal, path + timestamp only — no IP, no user id, no cookie.
        // Enough for a traffic count without turning this into a tracking
        // system.
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->timestamp('created_at');

            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
