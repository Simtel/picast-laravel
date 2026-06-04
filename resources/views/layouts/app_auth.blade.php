<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>A&S Tech</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">
    <link rel="Shortcut Icon" href="/Hopstarter-Soft-Scraps-Image-JPEG.ico" type="image/x-icon"/>

    @vite(['resources/assets/sass/app.scss', 'resources/assets/js/app.js'])
    
    <!-- Additional CSS Files (copied to public/css) -->
    @if(file_exists(public_path('css/auth.css')))
        <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    @endif
</head>
<body class="text-center">

{{----}}
<div class="container">
    @yield('content')
</div>
</body>
</html>