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
                        <h3>Forgot Password</h3>
                        <p class="text-muted mt-2">
                            Forgot your password? No problem. Enter your email and we’ll send you a reset link.
                        </p>
                    </div>

                    <!-- Status Message -->
                    @if (session('status'))
                        <div class="alert alert-success text-center mt-3">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="form-body mt-4">
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="row">
                                <div class="col-12">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email"
                                           name="email"
                                           id="email"
                                           class="form-control"
                                           placeholder="Enter your email"
                                           value="{{ old('email') }}"
                                           required
                                           autofocus>

                                    @error('email')
                                        <span class="text-danger">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-12 mt-4">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bx bx-mail-send"></i> Email Password Reset Link
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
