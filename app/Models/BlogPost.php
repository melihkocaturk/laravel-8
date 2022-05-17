<?php

namespace App\Models;

use App\Scopes\LatestScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class BlogPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'content', 'user_id'];
    
    public function comments()
    {
        return $this->hasMany('App\Models\Comment');

        // $blogpost->comments()->saveMany([$comment1, $comment2]);

        // $posts = BlogPost::with('comments')->get();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function scopeLatest(Builder $query)
    {
        return $query->order_by(static::CREATED_AT, 'desc');
    }

    public function scopeMostcommented(Builder $query)
    {
        return $query->withCount('comments')->orderBy('comments_count', 'desc');
    }

    public function image()
    {
        return $this->hasOne('App\Models\Image');
    }

    public static function boot()
    {
        parent::boot();

        // static::deleted(function (BlogPost $blogPost) {
        //     // dd('I was deleted');
        //     $blogPost->comments()->delete();
        // });

        // static::addGlobalScope(new LatestScope);

        static::updating(function (BlogPost $blogPost) {
            Cache::forget("blog-post-{$blogPost->id}");
        });
    }
}
