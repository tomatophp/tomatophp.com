<!doctype html >
<html class="dark motion-safe:scroll-smooth 2xl:text-[20px]" lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    @include('cms::parts.meta')
    @include('cms::parts.css')
    @include('cms::parts.pwa')
</head>
<body class="hidden app antialiased text-gray-900 dark:text-slate-300 tracking-tight bg-white dark:bg-slate-950 ibm-plex-sans-arabic-medium">
    @include('cms::parts.header')
    <main>
        @yield('body')
    </main>

    @include('cms::parts.footer')
    @include('cms::parts.js')
</body>
</html>
