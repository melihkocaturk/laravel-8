@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <form action="{{ route('register') }}" class="was-validated" method="POST">
        @csrf

        <div class="mb-3">
            <label for="" class="form-label">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required  
                class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}">
            @if ($errors->has('name'))
                <div class="invalid-feedback">
                    {{ $errors->first('name') }}
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="" class="form-label">E-mail</label>
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
            <label for="" class="form-label">Password Confirmation</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Register</button>
    </form>
@endsection