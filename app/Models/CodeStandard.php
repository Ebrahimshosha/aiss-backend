<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CodeStandard extends Model
{
    protected $table = 'code_standards';

    protected $appends = [
        'cover_image_url',
        'inner_image_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->getMediaUrl(
            $this->attributes['cover_image'] ?? null
        );
    }

    public function getInnerImageUrlAttribute(): ?string
    {
        return $this->getMediaUrl(
            $this->attributes['inner_image'] ?? null
        );
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