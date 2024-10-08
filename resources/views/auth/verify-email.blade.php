<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body class="antialiased">
    {{-- <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0"> --}}
    <div class="vh-100" style="background-color: #eee;">
        <div class="container h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-lg-12 col-xl-11">
                    <div class="card text-black" style="border-radius: 25px;">
                        <div class="card-body p-md-5">
                            <div class="row justify-content-center">
                                <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1">

                                    <p class="text-center h1 fw-bold mb-5 mx-1 mx-md-4 mt-4">
                                        {{ __('Verify Your Email Address') }}</p>

                                    <form id="verify-code-form"">
                                        <input type="text" id="code" placeholder="輸入驗證碼">
                                        <button type="button" onclick="verifyCode()">驗證</button>
                                        <div id="error-message" class="text-danger mt-2"></div>
                                    </form>

                                    @if (session('resent'))
                                        <div class="alert alert-success" role="alert">
                                            {{ __('A fresh verification link has been sent to your email address.') }}
                                        </div>
                                    @endif

                                    {{ __('Before proceeding, please check your email for a verification link.') }}
                                    {{ __('If you did not receive the email') }},
                                    <button type="button" onclick="resendCode()" class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.

                                </div>
                                <div class="col-md-10 col-lg-6 col-xl-7 d-flex align-items-center order-1 order-lg-2">

                                    <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-registration/draw1.webp"
                                        class="img-fluid" alt="Sample image">

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
        function resendCode() {
            axios.post('/email/resend')
            .then(function(response) {
                alert(response.data.message);
            })
            .catch(function(error) {
                const errorMessage = document.getElementById('error-message');
                errorMessage.textContent = error.response.data.message;
            });
        }
        function verifyCode() {
            const code = document.getElementById('code').value;
            axios.post('/verify-email', { code: code })
                .then(response => {
                    window.location.href = '/home';
                })
                .catch(error => {
                    const errorMessage = document.getElementById('error-message');
                    errorMessage.textContent = error.response.data.message || 'An error occurred';
                });
        }
    </script>
</body>

</html>
