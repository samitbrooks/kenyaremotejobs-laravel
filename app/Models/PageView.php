<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['path', 'created_at'])]
class PageView extends Model
{
    // Only created_at exists on this table (see the migration) — this is
    // the one case the Table(timestamps:) attribute can't express, since it
    // toggles both timestamp columns together.
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }
}
