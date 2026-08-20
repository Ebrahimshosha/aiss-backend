<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    protected $fillable = [
        'name',
        'email',
        'body',
       
    ];

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}