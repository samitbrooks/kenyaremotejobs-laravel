<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sync_meta', function (Blueprint $table) {
            $table->tinyInteger('id')->primary();
            $table->timestamp('last_synced_at')->nullable();
        });

        DB::table('sync_meta')->insert(['id' => 1, 'last_synced_at' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_meta');
    }
};
