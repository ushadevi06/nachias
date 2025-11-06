<!DOCTYPE html>
<html
    lang="en"
    class=" layout-navbar-fixed layout-menu-fixed layout-compact "
    dir="ltr"
    data-skin="default"
    data-assets-path="../../assets/"
    data-template="horizontal-menu-template"
    data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nachias</title>
    <link rel="stylesheet" href="{{ url('assets/css/core.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/demo.css') }}">
    <link rel="stylesheet" href="{{ url('assets/fonts/iconify-icons.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/node-waves.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/responsive.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/select2.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/page-auth.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>

<body>
    <div class="authentication-wrapper authentication-cover">
        <!--   <a href="{{ url('') }}" class="auth-cover-brand d-flex align-items-center gap-3">
            <span class="app-brand-text demo text-heading fw-semibold">Nachias</span>
        </a> -->
        <div class="authentication-inner row m-0">
            <div class="d-none d-lg-flex col-lg-5 align-items-center justify-content-center p-0 login-background">
                <img src="{{ url('assets/images/login.png') }}"
                    class="img-fluid"
                    alt="login background" />
            </div>
            <!-- Login -->
            <div class="d-flex col-12 col-lg-7 align-items-center authentication-bg position-relative py-sm-5 px-4 py-4">
                <div class="login-card">
                    <h4 class="login-logo">Nachias</h4>
                    <h5>Welcome to Nachias! </h5>
                    <div class="col-lg-12">
                        @include('flash_messages')
                    </div>
                    <form id="formAuthentication" class="mb-5" action="{{ url('login') }}" method="POST" autocomplete="off">
                        @csrf
                        <div class="form-floating form-floating-outline mb-5 form-control-validation">
                            <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email" value="{{ old('email', Cookie::get('email') ?? '') }}" />
                            <label for="email">Email</label>
                            <span class="text-danger error-msg">{{ $errors->first('email') }}</span>
                        </div>
                        <div class="mb-5">
                            <div class="form-password-toggle form-control-validation">
                                <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                        <input type="password" name="password" id="form-alignment-password" class="form-control" placeholder="············" aria-describedby="form-alignment-password2" onkeypress="return event.charCode !== 32" value="{{ old('password', Cookie::get('password') ?? '') }}">
                                        <label for="form-alignment-password">Password</label>
                                    </div>
                                    <span class="input-group-text cursor-pointer" id="form-alignment-password2"><i class="icon-base ri ri-eye-off-line"></i></span>
                                </div>
                                <span class="text-danger error-msg">{{ $errors->first('password') }}</span>
                            </div>
                        </div>
                        <div class="mb-5 d-flex justify-content-between flex-wrap py-2">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" value="1" name="remember" id="remember-me" {{ Cookie::get('remember') ? 'checked' : '' }} />
                                <label class="form-check-label me-2" for="remember-me"> Remember Me </label>
                            </div>
                        </div>
                        <button class="btn btn-primary d-grid w-100" type="submit">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ url('assets/js/jquery.js') }}"></script>
    <script src="{{ url('assets/js/config.js') }}"></script>
    <script src="{{ url('assets/js/helpers.js') }}"></script>
    <script src="{{ url('assets/js/menu.js') }}"></script>
    <script src="{{ url('assets/js/main.js') }}"></script>
    <script src="{{ url('assets/js/select2.js') }}"></script>
    <script src="{{ url('assets/js/bootstrap.js') }}"></script>
</body>

</html>