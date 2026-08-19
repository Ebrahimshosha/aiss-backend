<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'user_articles';

    protected $appends = [
        'cover_image_url',
        'inner_image_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,
            'article_tags',
            'article_id',
            'tag_id'
        );
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

        // لو متخزن أصلًا Full URL
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $path = ltrim($path, '/');

        // الملفات القديمة من PHP Native
        if (str_starts_with($path, 'assets/')) {
            return asset($path);
        }

        // لو القيمة فيها storage/ بالفعل
        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        // الملفات الجديدة التي يخزنها Laravel على public disk
        return asset('storage/' . $path);
    }
}
