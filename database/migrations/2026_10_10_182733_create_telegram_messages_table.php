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
        Schema::create('telegram_messages', function (Blueprint $table) {
            $table->id();
            $table->string('chat_id')->index();
            $table->string('chat_type', 30)->default('group'); // 'private', 'group', 'supergroup', 'channel'
            $table->string('from_user_id')->nullable()->index();
            $table->string('from_username')->nullable();
            $table->string('direction', 20)->default('outbound'); // 'inbound' or 'outbound'
            $table->string('message_type', 50)->default('text'); // 'text', 'command', 'ai_reply', 'broadcast', 'welcome'
            $table->text('content');
            $table->string('status', 30)->default('sent'); // 'sent', 'failed', 'received'
            $table->string('telegram_message_id')->nullable()->index();
            $table->json('raw_payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_messages');
    }
};
