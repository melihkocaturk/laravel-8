<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePost;
use App\Models\BlogPost;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // DB::connection()->enableQueryLog();

        // // $posts = BlogPost::all();

        // $posts = BlogPost::with('comments')->get();

        // foreach ($posts as $post) {
        //     foreach ($post->comments as $comment) {
        //         echo $comment;
        //     }
        // }

        // dd(DB::getQueryLog());

        // return view('posts.index', ['posts' => BlogPost::all()]);

        $mostCommented = Cache::remember('blogpost-most-commented', now()->addSeconds(10), function () {
            return BlogPost::mostCommented()->take(5)->get();
        });

        return view(
            'posts.index', [
                'posts' => BlogPost::latest()->withCount('comments')->with('user')->get(),
                'mostCommented' => $mostCommented,
            ] 
        );
        
        
        // Relationship existence
        // BlogPost::has('comments')->get(); 
        // BlogPost::has('comments', '>=', 2)->get();

        // Relationship absence
        // BlogPost::doesntHave('comments')->get();

        // with Count
        // BlogPost::withCount('comments')->get();

        // Soft Deleted Posts
        // BlogPost::withTrashed()->get();
        // BlogPost::onlyTrashed()->get();
        // BlogPost::withTrashed()->where('id', 1)->restore();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePost $request)
    {
        // $request->validate([
        //     'title' => 'required|min:10|max:100',
        //     'content' => 'required|min:10'
        // ]);

        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        // $user = $request->user();
        // $post = $user->blogPosts()->create($validated);
        
        $post = BlogPost::create($validated);

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails');

            // Image::create([
            //     'path' => $path,
            //     'blog_post_id' => $post->id,
            // ]);

            $post->image()->create(['path' => $path]);

            // $file = $request->file('thumbnail');
            // dump($file);
            // dump($file->getClientMimeType());
            // dump($file->getClientOriginalExtension());

            // $file->store('thumbnails');
            // dump(Storage::disk('public')->put('thumbnails', $file));
            // dump($file::storeAs('thumbnails', $post->id . '.'  $file->guessExtension()));
            // $name = Storage::putFileAs('thumbnails', $file, $post->id . '.' . $file->guessExtension());
            // dump(Storage::url($name));
        }

        // die;

        // $post = new BlogPost();
        // $post->title = request()->input('title');
        // $post->content = request()->input('content');
        // $post->title = $validated['title'];
        // $post->content = $validated['content'];
        // $post->save();

        $request->session()->flash('status', 'The blog post was created');

        return redirect()->route('posts.show', ['post' => $post->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // abort_if(!isset($this->posts[$id]), 404);
        // return view('posts.show', ['post' => BlogPost::findOrFail($id)]);

        // return view('posts.show', [
        //     'post' => BlogPost::with('comments')->findOrFail($id)
        // ]);

        $blogPost = Cache::remember("blog-post-{$id}", 60, function () use($id) {
            return BlogPost::with(['comments' => function ($query) {
                return $query->latest();
            }])->findOrFail($id);
        });

        $sessionId = session()->getId();
        $counterKey = "blog-post-{$id}-counter";
        $usersKey = "blog-post-{$id}-users";

        $users = Cache::get($usersKey, []);
        $usersUpdate = [];
        $difference = 0;
        $now = now();

        foreach ($users as $session => $lastVisit) {
            if ($now->diffInMinutes($lastVisit) >= 1) {
                $difference--;
            } else {
                $usersUpdate[$session] = $lastVisit;
            }
        }

        if (!array_key_exists($sessionId, $users) || $now->diffInMinutes($users[$sessionId]) >= 1) {
            $difference++;
        }

        $usersUpdate[$sessionId] = $now;

        Cache::forever($usersKey, $usersUpdate);

        if (!Cache::has($counterKey)) {
            Cache::forever($counterKey, 1);
        } else {
            Cache::increment($counterKey, $difference);
        }

        $counter = Cache::get($counterKey);

        return view('posts.show', [
            'post' => $blogPost,
            'counter' => $counter,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = BlogPost::findOrFail($id);

        // if (Gate::denies('update-post', $post)) {
        //     abort(403, "You can't edit this blog post!");
        // }

        $this->authorize('posts.update', $post);

        return view('posts.edit', ['post' => $post]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StorePost $request, $id)
    {

        $post = BlogPost::findOrFail($id);

        // if (Gate::denies('update-post', $post)) {
        //     abort(403, "You can't edit this blog post!");
        // }

        $this->authorize('posts.update', $post);

        $validated = $request->validated();
        $post->fill($validated);
        $post->save();

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails');

            if ($post->image) {
                Storage::delete($post->image->path);
                $post->image->path = $path;
                $post->image->save();
            } else {
                $post->image()->create(['path' => $path]);
            }
        }

        $request->session()->flash('status', 'Blog post was updated');
        return redirect()->route('posts.show', ['post' => $post->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = BlogPost::findOrFail($id);

        // if (Gate::denies('delete-post', $post)) {
        //     abort(403, "You can't delete this blog post!");
        // }

        $this->authorize('posts.delete', $post);

        // Gate::forUser($user)->denies('delete-post', $post);
        // Gate::forUser($user)->allows('delete-post', $post);

        $post->delete();

        session()->flash('status', 'Blog post was deleted');
        return redirect()->route('posts.index');
    }
}
