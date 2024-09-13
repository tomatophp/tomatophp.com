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

        <div class="fixed bottom-4 right-4">
            <a href="https://wa.me/{{setting('site_phone')}}" target="_blank" title="{{ trans('cms::messages.share.networks.whatsapp') }}">
                <div class="bg-success-400 hover:bg-success-500 text-white w-10 h-10 font-bold rounded-full shadow-lg flex flex-col justify-center items-center">
                    <x-icon name="bxl-whatsapp" class="w-6 h-6" />
                </div>
            </a>
        </div>
    </main>

    @include('cms::parts.footer')
    @include('cms::parts.js')
</body>
</html>
