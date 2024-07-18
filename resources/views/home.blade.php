<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
      <!-- Font Awesome -->
      <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
      <!-- Google Fonts -->
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Chocolate+Classical+Sans&display=swap" rel="stylesheet">
      <style>
        .avatar-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
        }

        @media (max-width: 767px) {
            .avatar-img {
                width: 100px;
                height: 100px;
            }
        }
    </style>

</head>

<body class="antialiased">
    <div class="vh-100" style="background-color: #eee;">
        <div class="container h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-lg-12 col-xl-11">
                    <div class="card text-black" style="border-radius: 25px;">
                        <div class="card-body p-md-5">
                            <div class="row justify-content-center">
                                <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1">

                                    <p class="text-center h1 fw-bold mb-5 mx-1 mx-md-4 mt-4">{{ __('auth.ResetPassword') }}</p>
                                    <form class="mx-1 mx-md-4" action="{{ route('user.updateAvatar') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class=" d-flex align-items-center">
                                                <img src="{{ asset('storage/avatars/' . Auth::user()->avatar) }}" alt="User Avatar" class="img-thumbnail rounded-circle avatar-img">
                                            </div>
                                            <button type="submit" class="btn btn-primary align-self-end">{{ __('auth.UploadAvatar') }}</button>
                                        </div>

                                        <div class="mb-4">
                                            <input type="file" name="avatar" class="form-control" required>
                                        </div>
                                    </form>
                                    <form class="mx-1 mx-md-4" method="POST" action="{{ route('user.resetPassword') }}">
                                        @csrf

                                        <div class="d-flex flex-row align-items-center mb-4">
                                            <label class="fas fa-key fa-lg me-3 fa-fw"
                                                for="OldPassword"></label>
                                            <div class="form-outline flex-fill mb-0">
                                                <input type="OldPassword" id="OldPassword" class="form-control" required
                                                    name="old_password" placeholder="{{ __('auth.OldPassword') }}"
                                                    value="{{ old('old_password') }}" />
                                                <div id="error-message" class="text-danger mt-2"></div>
                                                @error('old_password')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="d-flex flex-row align-items-center mb-4">
                                            <label class="fas fa-lock fa-lg me-3 fa-fw" for="userPassword"></label>
                                            <div class="form-outline flex-fill mb-0">
                                                <input type="password" id="userPassword" class="form-control" required
                                                    name="password" placeholder="{{ __('auth.NewPassword') }}" />
                                                @error('password')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="d-flex flex-row align-items-center mb-4">
                                            <label class="fas fa-lock fa-lg me-3 fa-fw" for="userRePassword"></label>
                                            <div class="form-outline flex-fill mb-0">
                                                <input type="password" id="userRePassword" class="form-control" required
                                                    name="password_confirmation" placeholder="{{ __('auth.RepeatPassword') }}" />
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
                                            <button type="submit" class="btn btn-primary btn-lg">{{ __('auth.ResetPassword') }}</button>
                                        </div>

                                    </form>

                                    @if (session('status'))
                                        <div class="alert alert-success">
                                            {{ session('status') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-10 col-lg-6 col-xl-7 d-flex align-items-center order-1 order-lg-2">
                                    <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-registration/draw1.webp" class="img-fluid" alt="Sample image">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
    <script>

    </script>
</body>

</html>
