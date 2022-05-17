@extends('layouts.app')

@section('title', $post->title)

@section('content')
    {{-- @unless ($post['is_new'])
        <div>old blog post!</div>
    @endunless --}}

    <h1>{{ $post->title }}</h1>

    @if ($post->image)
        <img src="{{ $post->image->url() }}" class="img-fluid my-3" alt="Image">
    @endif
    
    <p>{{ $post->content }}</p>
    <p>Added {{ $post->created_at->diffForHumans() }}</p>

    {{-- @isset($post['has_comments'])
        <div>the post has some comments...</div>
    @endisset --}}

    <p>Currently read by counter {{ $counter }} people</p>

    <h4>Comments</h4>
    @forelse ($post->comments as $comment)
        <p>{{ $comment->content }}, <span class="text-muted">added {{ $comment->created_at->diffForHumans() }}</span></p>
    @empty
        <p>No comments yet.</p>
    @endforelse
@endsection