<!DOCTYPE html>
<html lang="id">

<head>
    @php($assetBaseUrl = rtrim(request()->getSchemeAndHttpHost() . request()->getBaseUrl(), '/'))
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — Universitas Dinamika Surabaya</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ $assetBaseUrl }}/assets/css/bootstrap.css">

    <link rel="stylesheet" href="{{ $assetBaseUrl }}/assets/vendors/dripicons/webfont.css">
    <link rel="stylesheet" href="{{ $assetBaseUrl }}/assets/css/pages/dripicons.css">
    <link rel="stylesheet" href="{{ $assetBaseUrl }}/assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="{{ $assetBaseUrl }}/assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ $assetBaseUrl }}/assets/css/app.css?v=20260906-2">
    <link rel="stylesheet" href="{{ $assetBaseUrl }}/assets/css/spl-admin.css?v=20260906-2">
    <link rel="shortcut icon" href="{{ $assetBaseUrl }}/assets/images/favicon.svg">
</head>

<body class="spl-app">
    <div id="app">
        @include('layouts.partials.sidebar')
        <div id="main" class='layout-navbar'>
            @include('layouts.partials.navbar')
            <div id="main-content">

                @yield('content')

                @include('layouts.partials.footer')
            </div>
        </div>
    </div>
    <script src="{{ $assetBaseUrl }}/assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="{{ $assetBaseUrl }}/assets/js/bootstrap.bundle.min.js"></script>
    <script src="{{ $assetBaseUrl }}/assets/js/main.js"></script>
</body>

</html>
