<?php
   use App\apptemplate as apps;
   $mode = config('app.app_mode', '-');
?>
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', '-') }}</title>

    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/default/assets/images/'.apps::gettemplate($mode, 'favicon')) }}">
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous"> --}}
    {{-- <link href="{{ asset('/js/toast-master/css/jquery.toast.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('/default/dist/css/style-new.css') }}" rel="stylesheet"> --}}
    {{-- <script src="{{ asset('/js/jquery/jquery-3.2.1.min.js') }}"></script> --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>

    {{-- <link href="{{ asset('/js/toastr/toastr.min.css') }}" rel="stylesheet"> --}}
    {{-- <script src="{{ asset('/js/toastr/toastr.min.js') }}"></script> --}}

    {{-- <link href="{{ asset('/css/customs.css') }}" rel="stylesheet"> --}}
    {{-- <link href="{{ asset('/default/dist/css/pages/login-register-lock.css') }}" rel="stylesheet"> --}}
    {{-- <link rel="stylesheet" href="{{asset('dist/auth/css/styles.css')}}"> --}}

    <!-- Bootstrap Core CSS -->
    <link href="{{asset('bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('/dist/plugins/bower_components/bootstrap-extension/css/bootstrap-extension.css')}}" rel="stylesheet">
    <!-- animation CSS -->
    <link href="{{asset('css/animate.css')}}" rel="stylesheet">
    <!-- color CSS -->
    <link href="{{asset('css/colors/default.css')}}" id="theme" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{asset('css/style.css')}}" rel="stylesheet">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.5.1/sweetalert2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.5.1/sweetalert2.all.min.js"></script>

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    
<![endif]-->
<style>
    .login-register {
        background: url('../assets/img/background.jpeg') center center/cover no-repeat !important;
        height: 100%;
        position: fixed;
        /* background-size: 70%; */
    }
</style>
</head>

<body class="{{ apps::gettemplate($mode, 'main_theme') }}">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">{{ config('app.name', '-') }}</p>
        </div>
    </div>
    <header>
         <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        {{-- <header class="topbar">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <!-- ============================================================== -->
                <!-- Logo -->
                <!-- ============================================================== -->
                <div class="navbar-header">
                    <a class="navbar-brand" href="">
                        <!-- Logo icon --><b>
                            <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                            <!-- Dark Logo icon -->
                            <img src="{{ asset('/default/assets/images/'.apps::gettemplate($mode, 'logo-32')) }}" alt="homepage" class="dark-logo" />
                            <!-- Light Logo icon -->
                            <img src="{{ asset('/default/assets/images/'.apps::gettemplate($mode, 'logo-32')) }}" alt="homepage" class="light-logo" />
                        </b>
                        <!--End Logo icon -->
                        <span class="hidden-xs"><span class="font-bold">{{ apps::gettemplate($mode, 'app_alias2') }}</span>
                    </a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav mr-auto">
                    </ul>
                    <!-- ============================================================== -->
                    <!-- User profile and search -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav my-lg-0">
                        <!-- ============================================================== -->
                        <!-- User Profile -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown u-pro">
                            <a class="nav-link dropdown-toggle waves-effect waves-dark profile-pic" href="javascript:;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="text-white">FAQ</span> 
                            </a>
                        </i>
                        <li class="nav-item dropdown u-pro">
                            <a class="nav-link dropdown-toggle waves-effect waves-dark profile-pic" href="javascript:;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="text-white">Video Tutorial</span> 
                            </a>
                        </i>
                        <li class="nav-item dropdown u-pro">
                            <a class="nav-link dropdown-toggle waves-effect waves-dark profile-pic" href="javascript:;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="text-white">Download &nbsp;<i class="fa fa-angle-down"></i></span> 
                            </a>
                            <div class="dropdown-menu dropdown-menu-right animated flipInY">
                                <!-- text-->
                                <a href="javascript:;" class="dropdown-item">Dasar Hukum</a>
                                <a href="javascript:;" class="dropdown-item">Manual Guide</a>
                                <a href="javascript:;" class="dropdown-item">Video Tutorial</a>
                            </div>
                        </li>
                        <!-- ============================================================== -->
                        <!-- End User Profile -->
                        <!-- ============================================================== -->
                    </ul>
                </div>
            </nav>
        </header> --}}
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss - section style="background-image:url({{ asset('/images/news/sipd-kapitasi-1.jpg') }});"-->
    <!-- ============================================================== -->
    <section id="wrapper" class="login-register login-sidebar">
        <!-- ============================================================== -->
        <!-- Content -->
        <!-- ============================================================== -->
                @yield('content')
        <!-- ============================================================== -->
        <!-- End Content -->
        <!-- ============================================================== -->
    </section>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    </header>
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script>
        @if(Session::has('alert-success'))
            toastr.success("{{ Session::get('alert-success') }}")
        @endif
        @if(Session::has('alert-danger'))
            toastr.warning("{{ Session::get('alert-danger') }}")
        @endif

        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    </script>

    <script src="{{ asset('/js/customs.js') }}"></script>
    <script src="{{ asset('/js/popper/popper.min.js') }}"></script>
    <script src="{{ asset('/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    {{-- <script src="{{ asset('/default/dist/js/perfect-scrollbar.jquery.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('/default/dist/js/custom.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('/default/dist/js/sweetalert2.min.js') }}"></script> --}}
    <script src="{{ asset('/js/toast-master/js/jquery.toast.js') }}"></script>
    <!--Custom JavaScript -->
    <script type="text/javascript">
        $(function() {
            $(".preloader").fadeOut();
        });
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        });
        // ============================================================== 
        // Login and Recover Password 
        // ============================================================== 
        $('#to-recover').on("click", function() {
            $("#loginform").slideUp();
            $("#recoverform").fadeIn();
        });
    </script>
    
</body>

</html>
