<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="DocDot - Konsultasikan keluhan kesehatan Anda dengan AI terpercaya. Dapatkan informasi obat, artikel kesehatan, dan tracking kondisi fisik & mental Anda. Gratis 24 jam.">
    <meta name="keywords"
        content="kesehatan, konsultasi dokter, AI kesehatan, informasi obat, artikel kesehatan, tracking kesehatan, DocDot">
    <meta name="author" content="DocDot">

    <title inertia>{{ config('app.name', 'DocDot') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>
