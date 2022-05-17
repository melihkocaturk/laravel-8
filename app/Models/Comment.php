<?php

namespace App\Models;

use App\Scopes\LatestScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Comment extends Model
{
    use HasFactory;

    // blog_post_id
    public function blogPost()
    {
        return $this->belongsTo('App\Models\BlogPost');

        // $bp->comments()->save($comment);
        // $comment->blogPost()->associate($blog_post)->save();
        // $comment->blog_post_id = $blog_post->id;

        // $comment->blogPost;
    }

    public function scopeLatest(Builder $query)
    {
        return $query->order_by(static::CREATED_AT, 'desc');
    }

    public static function boot()
    {
        parent::boot();

        // static::addGlobalScope(new LatestScope);
    }
}
