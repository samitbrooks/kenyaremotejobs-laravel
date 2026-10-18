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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 32)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name', 120);
            $table->string('email', 180)->index();
            $table->string('category', 50)->default('general')->index();
            $table->string('priority', 20)->default('normal')->index();
            $table->string('status', 20)->default('open')->index();
            $table->string('subject', 200);
            $table->text('message');
            $table->string('access_token', 64)->unique();
            $table->timestamp('last_replied_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
