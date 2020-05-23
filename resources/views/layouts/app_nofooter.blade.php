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

    <title>Laravel</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Product+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

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
                            <a class="nav-link" href="/#pricing">Planes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/#">Contacto</a>
                        </li>

                        <li class="nav-item text-center">
                            <a href="/login" class="btn align-middle btn-outline-primary">Iniciar sesión</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- // end navbar -->

        @yield('hero')

        <div class="bg-shape"></div>
        <div class="bg-circle"></div>
        <div class="bg-circle-two"></div>
    </header>

    <main id="app">
        @yield('content')
    </main>

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
