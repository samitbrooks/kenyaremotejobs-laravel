<?php

namespace App\Models;

use Database\Factories\CreditPurchaseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(keyType: 'string', incrementing: false, timestamps: false)]
#[Fillable(['id', 'user_id', 'tier', 'credits', 'amount_kes', 'purchased_at'])]
class CreditPurchase extends Model
{
    /** @use HasFactory<CreditPurchaseFactory> */
    use HasFactory, HasUuids;

    protected function casts(): array
    {
        return ['purchased_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
