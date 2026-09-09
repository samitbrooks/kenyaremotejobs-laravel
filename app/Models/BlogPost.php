<?php

namespace App\Models;

use Database\Factories\BlogPostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\RouteKey;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Table(keyType: 'string', incrementing: false)]
#[Fillable(['id', 'slug', 'title', 'excerpt', 'content', 'category', 'author_name', 'published'])]
#[RouteKey('slug')]
class BlogPost extends Model
{
    /** @use HasFactory<BlogPostFactory> */
    use HasFactory, HasUuids;

    protected function casts(): array
    {
        return ['published' => 'boolean', 'published_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        // Set once, on whichever save first flips published to true —
        // covers the create form, the edit form, and the admin toggle
        // action alike without duplicating this in all three. Deliberately
        // never overwritten on a later republish: it means "first went
        // live", not "last saved while published".
        static::saving(function (BlogPost $post) {
            if ($post->published && $post->published_at === null) {
                $post->published_at = now();
            }
        });
    }
}
