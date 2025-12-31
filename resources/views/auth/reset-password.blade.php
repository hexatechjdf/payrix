@extends('layouts.guest')

@section('content')
<div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
    <div class="col mx-auto">
        <div class="mb-4 text-center">
            <img src="{{ asset('assets/images/logo-img.png') }}" width="180" alt="Logo" />
        </div>

        <div class="card rounded-4">
            <div class="card-body">
                <div class="p-4 rounded">

                    <div class="text-center">
                        <h3>Reset Password</h3>
                        <p class="text-muted mt-2">
                            Enter your email and new password to reset your account password.
                        </p>
                    </div>

                    <div class="form-body mt-4">
                        <form method="POST" action="{{ route('password.store') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ request()->route('token') }}">

                            <div class="row">

                                <div class="col-12">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email"
                                           name="email"
                                           id="email"
                                           class="form-control"
                                           placeholder="Enter your email"
                                           value="{{ old('email', request()->email) }}"
                                           required
                                           autofocus>

                                    @error('email')
                                        <span class="text-danger">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-12 mt-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password"
                                           name="password"
                                           id="password"
                                           class="form-control"
                                           placeholder="Enter new password"
                                           required>

                                    @error('password')
                                        <span class="text-danger">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-12 mt-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password"
                                           name="password_confirmation"
                                           id="password_confirmation"
                                           class="form-control"
                                           placeholder="Confirm new password"
                                           required>

                                    @error('password_confirmation')
                                        <span class="text-danger">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-12 mt-4">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bx bx-lock-open"></i> Reset Password
                                        </button>
                                    </div>
                                </div>

                                <div class="col-12 text-center mt-3">
                                    <a href="{{ route('login') }}">
                                        Back to Login
                                    </a>
                                </div>

                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
