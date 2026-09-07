<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table(keyType: 'string', incrementing: false, timestamps: false)]
#[Fillable(['id', 'hidden_at'])]
class HiddenJob extends Model
{
    protected function casts(): array
    {
        return ['hidden_at' => 'datetime'];
    }
}
