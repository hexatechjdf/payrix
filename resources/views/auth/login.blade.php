@extends('layouts.guest')
@section('content')
    <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
        <div class="col mx-auto">
            <div class="mb-4 text-center">
                <img src="assets/images/logo-img.png" width="180" alt="" />
            </div>
            <div class="card rounded-4">
                <div class="card-body">
                    <div class="p-4 rounded">
                        <div class="text-center">
                            <h3 class="">Sign in</h3>
                        </div>

                        <div class="login-separater text-center mb-4"> <span>SIGN IN WITH EMAIL</span>
                            <hr />
                        </div>
                        <div class="form-body">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="row">
                                    <div class="col-12">
                                        <label for="inputEmailAddress" class="form-label">Email Address</label>
                                        <input type="email" name="email" class="form-control" id="inputEmailAddress"
                                            placeholder="Email Address" value="{{ old('email') }}">
                                        @error('email')
                                            <span class="text-danger"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="inputChoosePassword" class="form-label">Enter Password</label>
                                        <div class="input-group" id="show_hide_password">
                                            <input type="password" name="password" class="form-control border-end-0"
                                                id="inputChoosePassword" placeholder="Enter Password">
                                            <a href="javascript:;" class="input-group-text bg-transparent">
                                                <i class="bx bx-hide"></i>
                                            </a>
                                        </div>
                                        @error('password')
                                            <span class="text-danger"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mt-2 mb-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                            <label class="form-check-label" for="remember">Remember Me</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 text-end mt-2 mb-2">
                                        @if (Route::has('password.request'))
                                            <a href="{{ route('password.request') }}">
                                                Forgot Password ?
                                            </a>
                                        @endif
                                    </div>

                                    <div class="col-12">
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bx bxs-lock-open"></i> Sign in
                                            </button>
                                        </div>
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
