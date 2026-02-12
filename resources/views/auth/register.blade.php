@extends('layouts.auth')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h3 class="font-weight-bold">Koleksi Buku</h3>
                        <p class="text-muted">Buat akun baru</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}" class="pt-3">
                        @csrf

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input id="name" type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Name">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password-confirm">Confirm Password</label>
                            <input id="password-confirm" type="password" class="form-control form-control-lg" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Password">
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary btn-block btn-lg font-weight-medium auth-form-btn">
                                REGISTER
                            </button>
                        </div>

                        <div class="text-center mt-3">
                            <span class="text-muted">Already have an account? </span>
                            <a href="{{ route('login') }}" class="text-primary font-weight-bold">Login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
