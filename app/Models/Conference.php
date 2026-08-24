<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conference extends Model
{
    protected $fillable = [
        'title',
        'year',
        'description',
        'image',
        'video_url',
    ];

    protected $appends = [
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
        ];
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (
            str_starts_with($this->image, 'http://') ||
            str_starts_with($this->image, 'https://')
        ) {
            return $this->image;
        }

        return asset('storage/' . ltrim($this->image, '/'));
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
