<li>
    @if(url()->current() === url(app()->getLocale() . '/'.$url))
        <a href="{{ str($url)->contains(config('app.domain')) ?  url(app()->getLocale() . '/'.$url) : $url }}" target="{{ str($url)->contains(config('app.domain')) ? '_blank' : '_self' }}" class="underline underline-offset-[7px] decoration-[3px] decoration-main font-medium hover:text-gray-900 dark:hover:text-white px-4 py-3 flex items-center transition duration-150 ease-in-out">
            {{ $label }}
        </a>
    @else
        <a href="{{ str($url)->contains(config('app.domain')) ?  url(app()->getLocale() . '/'.$url) : $url }}" target="{{ str($url)->contains(config('app.domain')) ? '_blank' : '_self' }}" class="font-medium hover:text-gray-900 dark:hover:text-white px-4 py-3 flex items-center transition duration-150 ease-in-out">
            {{ $label }}
        </a>
    @endif
</li>
