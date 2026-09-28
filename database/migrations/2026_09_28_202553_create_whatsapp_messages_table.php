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
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone_number')->index();
            $table->string('direction', 20)->default('outbound'); // 'inbound' or 'outbound'
            $table->string('message_type', 50)->default('text'); // 'text', 'template', 'job_alert'
            $table->text('content');
            $table->string('status', 30)->default('sent'); // 'sent', 'delivered', 'read', 'failed', 'received'
            $table->string('whatsapp_message_id')->nullable()->index();
            $table->json('raw_payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
