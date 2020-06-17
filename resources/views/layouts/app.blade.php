<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Meta Share -->
    <meta property="og:title" content="iTweetlytics — Agency One Page Parallax Template" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="images/screen.jpg" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Product+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/images/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="/images/favicon-16x16.png" sizes="16x16" />

    <!-- Styles -->
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="/css/template.css">

    <!-- Plugins -->
    <link rel="stylesheet" href="css/global/plugins/icon-font.css">
    <script src="https://kit.fontawesome.com/dffb68d74f.js" crossorigin="anonymous"></script>
</head>
<body>
    <header id="home" class="overflow-hidden">

        <!-- navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <a class="navbar-brand" href="/">
                    <img src="/images/logo.png" class="logo" alt="iTweetlytics logo">
                    <h3 class="gradient-mask">iTweetlytics</h3>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#site-nav" aria-controls="site-nav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="site-nav">
                    <ul class="navbar-nav text-sm-left ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/#features">Características</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="/#footer">Ayuda</a>
                        </li>

                        <li class="nav-item text-center">
                            <a href="login" class="btn align-middle btn-outline-primary btn-login">Iniciar sesión</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- // end navbar -->

    </header>

    <main id="app">
        @yield('content')
    </main>

    <div class="section bg-light mt-4" id="footer">
        <div class="container">
            <div class="row">
                <div class="col-sm-4">
                    <img src="/images/logo.png" class="footer-logo" alt="iTweetlytics Logo" />
                    <!-- // end .lead -->
                </div>
                <!-- // end .col-sm-3 -->
                <div class="col-sm-2">
                    <ul class="list-unstyled footer-links ml-1">
                        <li>
                            <a href="#contact">Contacto</a>
                        </li>
                        <li>
                            <a href="#about">Sobre nosotros</a>
                        </li>
                        <li>
                            <a href="#contact">Privacidad</a>
                        </li>
                    </ul>
                </div>
                <!-- // end .col-sm-3 -->
                <div class="col-sm-2 offset-2">
                    <a href="#home" class="btn btn-sm btn-outline-primary ml-1">Volver al inicio</a>
                </div>
                <!-- // end .col-sm-3 -->
            </div>
            <!-- // end .row -->
            <div class=" text-center mt-4">
                <small class="text-muted">Copyright © <script type="text/javascript">document.write(new Date().getFullYear());</script> All rights reserved. iTweetlytics.</small>
            </div>
        </div>
        <!-- // end .container -->
    </div>
    <!-- // end #about.section -->
    <!-- // end .agency -->

    <!-- JS Files -->
    <!-- build:js js/app.min.js -->
    <!-- jQuery first, then Tether, then Bootstrap JS. -->
    <script src="/js/global/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="/js/global/bootstrap.bundle.min.js"></script>
    <!-- Main JS -->
    <script src="/js/script.js"></script>
    <!-- /build -->
</body>
</html>
