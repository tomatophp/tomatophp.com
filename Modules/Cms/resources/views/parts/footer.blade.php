<footer class="border-t border-gray-200 dark:border-slate-800">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="md:flex md:items-center md:justify-between py-6 md:py-8">
            <ul class="justify-center flex mb-4 md:order-1 -ml-2 md:ml-4 md:mb-0">
                @if(setting("site_social"))
                    @foreach(setting("site_social") as $item)
                        <x-cms-socail-icon url="{{$item['link']}}" icon="bxl-{{ $item['vendor'] }}"/>
                    @endforeach
                @endif
            </ul>
            <div class="hidden md:inline text-xs text-gray-700 mr-4 dark:text-slate-400">
                <div>
                    <q>{{ trans('cms::messages.footer.copyright') }}</q>
                </div>
                <div>
                    <p>©{{ trans('cms::messages.footer.open-source') }} <a class="text-main" href="https://github.com/tomatophp/tomatophp.com/blob/master/LICENSE.md" target="_blank">{{ trans('cms::messages.footer.mit') }}</a> {{ trans('cms::messages.footer.for') }} <a href="https://github.com/tomatophp/tomatophp.com" target="_blank" class="text-main">{{ trans('cms::messages.footer.link') }}</a> </p>
                </div>
            </div>
        </div>
    </div>
</footer>
