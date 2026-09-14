<?php

namespace App\Models;

use Database\Factories\BlogPostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\RouteKey;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Table(keyType: 'string', incrementing: false)]
#[Fillable(['id', 'slug', 'title', 'excerpt', 'content', 'category', 'author_name', 'image_url', 'published'])]
#[RouteKey('slug')]
class BlogPost extends Model
{
    /** @use HasFactory<BlogPostFactory> */
    use HasFactory, HasUuids;

    protected function casts(): array
    {
        return ['published' => 'boolean', 'published_at' => 'datetime'];
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ?: 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80',
        );
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
