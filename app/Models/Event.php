<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'event_date',
        'description',
        'image',
    ];

    protected $appends = [
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date:Y-m-d',
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
}