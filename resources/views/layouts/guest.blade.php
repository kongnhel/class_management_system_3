<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="icon" type="image/png" href="{{ asset('assets/image/nmu_Logo.png') }}">
        <title>{{ config('app.name', 'Class Management System') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@100;300;400;700;900&display=swap" rel="stylesheet">

        <!-- Icons -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

        <!-- Scripts (តែ១ដងប៉ុណ្ណោះ!) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body, nav, div {
                font-family: 'Battambang', sans-serif;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="fixed top-4 right-4 z-50">
            <a href="{{ route('locale.switch', app()->getLocale() === 'km' ? 'en' : 'km') }}" 
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/90 backdrop-blur-sm rounded-lg shadow-sm border border-gray-200 text-xs font-bold text-gray-600 hover:text-emerald-600 hover:border-emerald-300 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                {{ app()->getLocale() === 'km' ? 'EN' : 'ខ្មែរ' }}
            </a>
        </div>
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            {{ $slot }}
        </div>
        <x-toast />
    </body>
</html>