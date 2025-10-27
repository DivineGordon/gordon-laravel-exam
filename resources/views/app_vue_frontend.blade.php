<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['frontend\page-customizer-frontend\src\style.css', 'frontend\page-customizer-frontend\src\main.ts'])
</head>
<body>
    <div id="app"></div>
</body>
</html>