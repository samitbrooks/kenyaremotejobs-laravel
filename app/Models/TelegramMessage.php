<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'chat_id',
    'chat_type',
    'from_user_id',
    'from_username',
    'direction',
    'message_type',
    'content',
    'status',
    'telegram_message_id',
    'raw_payload',
])]
class TelegramMessage extends Model
{
    use HasFactory;

    protected $table = 'telegram_messages';

    protected function casts(): array
    {
        return [
            'raw_payload' => 'array',
        ];
    }

    public function isInbound(): bool
    {
        return $this->direction === 'inbound';
    }

    public function isOutbound(): bool
    {
        return $this->direction === 'outbound';
    }
}
