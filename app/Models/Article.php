<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'user_articles';

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
}
