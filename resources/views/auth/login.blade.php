@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <form action="{{ route('login') }}" class="was-validated" method="POST">
        @csrf

        <div class="mb-3">
            <label for="" class="form-label">Username</label>
            <input type="text" name="email" value="{{ old('email') }}" class="form-control" required 
                class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}">
            @if ($errors->has('email'))
                <div class="invalid-feedback">
                    {{ $errors->first('email') }}
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required 
                class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}">
            @if ($errors->has('password'))
                <div class="invalid-feedback">
                    {{ $errors->first('password') }}
                </div>
            @endif
        </div>

        <div class="mb-3">
            <div class="form-check">
                <input type="checkbox" name="remember" id="remember" class="form-check-input" value="{{ old('remember') ? 'checked' : '' }}">
                <label for="remember" class="form-check-label">Remember Me</label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>
@endsection