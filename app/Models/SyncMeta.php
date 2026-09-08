<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table(name: 'sync_meta', timestamps: false)]
#[Fillable(['id', 'last_synced_at'])]
class SyncMeta extends Model
{
    protected function casts(): array
    {
        return ['last_synced_at' => 'datetime'];
    }
}
