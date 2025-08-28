<?php

namespace Fuelviews\SabHeroArticles\Traits;

use Fuelviews\SabHeroArticles\Models\Post;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

trait HasArticle
{
    /**
     * Boot the trait - auto-generate slugs and set author status
     */
    protected static function bootHasArticle()
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = $model->generateUniqueSlug($model->name);
            }

            // Set new users as authors by default
            if (! isset($model->is_author)) {
                $model->is_author = true;
            }
        });
    }

    /**
     * Generate a unique slug from the given name
     */
    private function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, config('sabhero-articles.user.foreign_key'));
    }

    /**
     * Use slug for route model binding
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Check if this user is marked as a public author
     */
    public function isAuthor(): bool
    {
        return (bool) $this->is_author;
    }

    /**
     * Scope to get only public authors
     */
    public function scopeAuthors($query)
    {
        return $query->where('is_author', true);
    }

    /**
     * Scope to get authors with published posts
     */
    public function scopeActiveAuthors($query)
    {
        return $query->authors()
            ->whereHas('posts', function ($q) {
                $q->where('status', 'published');
            });
    }
}
