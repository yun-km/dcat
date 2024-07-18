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

                                    <p class="text-center h1 fw-bold mb-5 mx-1 mx-md-4 mt-4">{{ __('auth.Register') }}</p>

                                    <form class="mx-1 mx-md-4" method="POST" action="{{ route('register.store') }}">
                                        @csrf

                                        <div class="d-flex flex-row align-items-center mb-4">
                                            <label class="fas fa-envelope fa-lg me-3 fa-fw"
                                                for="userEmail"></label>
                                            <div class="form-outline flex-fill mb-0">
                                                <input type="email" id="userEmail" class="form-control" required
                                                    name="email" placeholder="{{ __('auth.Email') }}"
                                                    value="{{ old('email') }}" />
                                                <div id="error-message" class="text-danger mt-2"></div>
                                                @error('email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="d-flex flex-row align-items-center mb-4">
                                            <label class="fas fa-envelope-open fa-lg me-3 fa-fw"
                                                for="userVerification"></label>
                                            <div class="form-outline flex-fill mb-0">
                                                <input type="text" id="uerVerification" class="form-control" required
                                                    name="verification" placeholder="{{ __('auth.Verification') }}"
                                                    value="{{ old('verification') }}" />
                                                @error('verification')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <button type="button" class="btn btn-primary btn-sm ms-2" onclick="sendCode()">{{ __('auth.Send') }}</button>
                                        </div>

                                        <div class="d-flex flex-row align-items-center mb-4">
                                            <label class="fas fa-lock fa-lg me-3 fa-fw" for="userPassword"></label>
                                            <div class="form-outline flex-fill mb-0">
                                                <input type="password" id="userPassword" class="form-control" required
                                                    name="password" placeholder="{{ __('auth.Password') }}" />
                                                @error('password')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="d-flex flex-row align-items-center mb-4">
                                            <label class="fas fa-key fa-lg me-3 fa-fw" for="userRePassword"></label>
                                            <div class="form-outline flex-fill mb-0">
                                                <input type="password" id="userRePassword" class="form-control" required
                                                    name="password_confirmation" placeholder="{{ __('auth.RepeatPassword') }}" />
                                            </div>
                                        </div>

                                        <div class="form-check d-flex justify-content-center mb-5">
                                            <input class="form-check-input me-2" type="checkbox" value="" id="form2Example3c" />
                                            <label class="form-check-label" for="form2Example3">
                                                I agree all statements in <a href="#!">Terms of service</a>
                                            </label>
                                        </div>

                                        <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
                                            <button type="submit" class="btn btn-primary btn-lg">{{ __('auth.Register') }}</button>
                                        </div>

                                    </form>

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
        function getDeviceInfo() {
            const userAgent = navigator.userAgent;
            const platform = navigator.platform;

            return {
                ip: '',
                device_id: getDeviceId(),
                platform: platform,
                browser: getBrowser(userAgent),
                os: getOS(userAgent)
            };
        }

        function getDeviceId() {
            let deviceId = localStorage.getItem('deviceId');
            if (!deviceId) {
                deviceId = 'device-' + Math.random().toString(36).substring(2, 15);
                localStorage.setItem('deviceId', deviceId);
            }
            return deviceId;
        }

        function getBrowser(userAgent) {
            if (userAgent.indexOf('Firefox') !== -1) return 'Firefox';
            if (userAgent.indexOf('Chrome') !== -1) return 'Chrome';
            if (userAgent.indexOf('Safari') !== -1) return 'Safari';
            if (userAgent.indexOf('MSIE') !== -1 || userAgent.indexOf('Trident') !== -1) return 'Internet Explorer';
            if (userAgent.indexOf('Edge') !== -1) return 'Edge';
            return 'Unknown';
        }

        function getOS(userAgent) {
            if (userAgent.indexOf('Windows NT 10.0') !== -1) return 'Windows 10';
            if (userAgent.indexOf('Windows NT 6.3') !== -1) return 'Windows 8.1';
            if (userAgent.indexOf('Windows NT 6.2') !== -1) return 'Windows 8';
            if (userAgent.indexOf('Windows NT 6.1') !== -1) return 'Windows 7';
            if (userAgent.indexOf('Mac OS X') !== -1) return 'Mac OS X';
            if (userAgent.indexOf('Linux') !== -1) return 'Linux';
            if (userAgent.indexOf('Android') !== -1) return 'Android';
            if (userAgent.indexOf('like Mac OS X') !== -1) return 'iOS';
            return 'Unknown';
        }

        function sendCode() {
            document.getElementById('error-message').textContent = '';
            const email = document.getElementById('userEmail').value;
            const deviceInfo = getDeviceInfo();

            axios.post('/verification/sendEmail', { email: email, deviceInfo: deviceInfo })
                .then(function(response) {
                    alert(response.data.message);
                })
                .catch(function(error) {
                    if (error.response && error.response.data && error.response.data.errors) {
                        const errors = error.response.data.errors;
                        if (errors.email) {
                            document.getElementById('error-message').textContent = errors.email[0];
                        }
                    }
                });
        }
    </script>
</body>

</html>
