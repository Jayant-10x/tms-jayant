@extends('layouts.auth', ['title' => 'Login'])

@section('content')
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        #bg-video {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -100; /* Pushes the video behind all other content */
            object-fit: cover; /* Forces the video to cover the screen without distorting */
        }

        .test-class {
            background: transparent;
            border-radius: 2rem;
            box-shadow: 0 6px 6px rgba(0, 0, 0, 0.2), 0 0 20px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 2.2);
            border: solid 1px;
            backdrop-filter: blur(4px);
            filter: url(#lensFilter) saturate(120%) brightness(1.15);
        }
    </style>

    <video autoplay muted loop playsinline id="bg-video" class="p-0">
        <source src="{{@asset('videos/login-bg.mp4')}}" type="video/mp4">
        Your browser does not support HTML5 video.
    </video>

    <div class="col-xl-5">
        <div class="card auth-card" style="background: none;">
            <div class="card-body px-3 py-5 test-class">
                <div class="mx-auto mb-4 text-center auth-logo">
            <span class="logo-dark">
                <img src="/images/tms-logo-light.png" height="70" alt="logo dark">
            </span>

                    <span class="logo-light">
                <img src="/images/tms-logo-light.png" height="70" alt="logo light">
            </span>
                </div>

                <h2 class="fw-bold text-uppercase text-center fs-18 text-light">Sign In</h2>

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
                            <label class="form-label text-light" for="login_user_name">Username <span
                                    class="text-danger">*</span></label>
                            {{-- Added old() helper to retain the typed username --}}
                            <input type="text" id="login_user_name" name="adm_user_name"
                                   class="form-control bg-light bg-opacity-50 border-light text-light py-2 @error('adm_user_name') is-invalid @enderror"
                                   placeholder="Enter your username" value="{{ old('adm_user_name') }}">

                            {{-- Kept this here only for blank/empty validation checks --}}
                            @error('adm_user_name')
                            @if($message == 'The adm user name field is required.')
                                <span class="validation-message text-danger fs-13">{{ $message }}</span>
                            @endif
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-light" for="password">Password <span
                                    class="text-danger">*</span></label>
                            <input type="password" id="password"
                                   class="form-control bg-light bg-opacity-50 border-light text-light py-2 @error('password') is-invalid @enderror"
                                   placeholder="Enter your password" name="password" value="">

                            @error('password')
                            <span class="validation-message text-danger fs-13">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-1 text-center d-grid">
                            <button class="btn btn-primary py-2 fw-medium" type="submit">Sign In</button>
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
