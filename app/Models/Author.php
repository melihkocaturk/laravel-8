<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    public function profile()
    {
        return $this->hasOne('App\Models\Profile');

        // $author->profile()->save($profile);
        // $author->profile // Lazy Loading
        // $author = Author::with('profile')->whereKey(1)->first();
        // $author = Author::with(['profile', 'comments'])->whereKey(1)->first();
    }
}
