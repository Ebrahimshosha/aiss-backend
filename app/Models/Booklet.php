<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Booklet extends Model
{
    public const UPDATED_AT = null;

    protected $appends = [
        'cover_image_url',
        'file_url',
    ];

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->getMediaUrl(
            $this->attributes['cover_image'] ?? null
        );
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->getMediaUrl(
            $this->attributes['file'] ?? null
        );
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    private function getMediaUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $path = str_replace('\\', '/', $path);

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'assets/')) {
            return asset($path);
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/' . $path);
    }
}
