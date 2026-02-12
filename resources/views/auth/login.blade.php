@extends('layouts.auth')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h3 class="font-weight-bold">Koleksi Buku</h3>
                        <p class="text-muted">Silakan login untuk melanjutkan</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="pt-3">
                        @csrf

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label text-muted" for="remember">
                                    Keep me signed in
                                </label>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary btn-block btn-lg font-weight-medium auth-form-btn">
                                LOGIN
                            </button>
                        </div>

                        <div class="text-center mt-4">
                            @if (Route::has('password.request'))
                                <a class="text-primary" href="{{ route('password.request') }}">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        @if (Route::has('register'))
                        <div class="text-center mt-3">
                            <span class="text-muted">Don't have an account? </span>
                            <a href="{{ route('register') }}" class="text-primary font-weight-bold">Register</a>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
