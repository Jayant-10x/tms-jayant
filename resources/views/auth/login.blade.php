@extends('layouts.auth', ['title' => 'Login'])

@section('content')
    <div class="col-xl-5">

        <div class="card auth-card">
            <div class="card-body px-3 py-5">
                <div class="mx-auto mb-4 text-center auth-logo">
            <span class="logo-dark">
                <img src="/images/logo-dark.png" height="32" alt="logo dark">
            </span>

                    <span class="logo-light">
                <img src="/images/logo-light.png" height="28" alt="logo light">
            </span>
                </div>

                <h2 class="fw-bold text-uppercase text-center fs-18">Sign In</h2>

                <div class="px-4">
                    {{-- ERROR MESSAGE DISPLAYED AT THE BOTTOM OF THE SIGN IN CONTENT --}}
                    @error('adm_user_name')
                    <div class="alert alert-danger text-center py-2 mb-3 fs-14" role="alert">
                        {{ $message }}
                    </div>
                    @enderror

                    <form method="POST" action="{{ route('login') }}" class="authentication-form" id="login_form">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="login_user_name">Username <span class="text-danger">*</span></label>
                            {{-- Added old() helper to retain the typed username --}}
                            <input type="text" id="login_user_name" name="adm_user_name"
                                   class="form-control bg-light bg-opacity-50 border-light py-2 @error('adm_user_name') is-invalid @enderror"
                                   placeholder="Enter your username" value="{{ old('adm_user_name') }}">

                            {{-- Kept this here only for blank/empty validation checks --}}
                            @error('adm_user_name')
                            @if($message == 'The adm user name field is required.')
                                <span class="validation-message text-danger fs-13">{{ $message }}</span>
                            @endif
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
                            <input type="password" id="password"
                                   class="form-control bg-light bg-opacity-50 border-light py-2 @error('password') is-invalid @enderror"
                                   placeholder="Enter your password" name="password" value="">

                            @error('password')
                            <span class="validation-message text-danger fs-13">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-1 text-center d-grid">
                            <button class="btn btn-danger py-2 fw-medium" type="submit">Sign In</button>
                        </div>
                    </form>
                </div> <!-- end col -->
            </div> <!-- end card-body -->
        </div>
        <!-- end card -->
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function () {
            $('#login_form').validate({
                rules: {
                    adm_user_name: {
                        required: true,
                        minlength: 6,
                        maxlength: 20,
                    },
                    password: {
                        required: true,
                        minlength: 8,
                        maxlength: 20,
                    },
                },
                messages: {
                    adm_user_name: {
                        required: 'Username is required.'
                    },
                    password: {
                        required: 'Password is required.'
                    },
                }
            });
        });
    </script>
@endpush
