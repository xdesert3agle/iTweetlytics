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
    <main id="app">
        @yield('content')
    </main>

    <!-- JS Files -->
    <!-- build:js js/app.min.js -->
    <!-- jQuery first, then Tether, then Bootstrap JS. -->
    <script src="js/global/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="js/global/bootstrap.bundle.min.js"></script>
    <!-- Main JS -->
    <script src="js/script.js"></script>
    <!-- /build -->
</body>
</html>
