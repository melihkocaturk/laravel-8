@extends('layouts.app')

@section('title', 'Posts')

@section('content')
    <div class="row">
        <div class="col-8">
            @each('posts.partials.post', $posts, 'post')

            {{-- @foreach ($posts as $post)
                @include('posts.partials.post')
            @endforeach --}}
        </div>
        <div class="col-4">
            <div class="card" style="width: 18rem;">
                <div class="card-header">
                    Most Commented
                </div>
                <ul class="list-group list-group-flush">
                    @foreach ($mostCommented as $post)
                        <li class="list-group-item">{{ $post->title }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection