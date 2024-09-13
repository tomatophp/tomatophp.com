@extends('cms::layouts.app')

@section('title', (app()->getLocale() === 'en' ? str(setting('site_name'))->explode('|')[0]??setting('site_name') : str(setting('site_name'))->explode('|')[1]??setting('site_name')) . ' | '.$page->title)
@section('description', $page->short_description)
@section('keywords', $page->keywords)
@if($page->getFirstMediaUrl('feature_image'))
    @section('image', $page->getFirstMediaUrl('feature_image'))
@endif

@section('body')
    <div class="bg-slate-50 dark:bg-inherit min-h-screen">
        <section class="container sm:px-6 py-12 sm:py-16 lg:py-20 mx-auto">
            <header>
                <h1 class="px-4 sm:px-6 max-w-3xl mx-auto text-center text-5xl md:text-6xl font-bold leading-tighter tracking-tighter mb-8 font-heading">
                    {{ $page->title }}
                </h1>
                <h2 class="px-4 sm:px-6 mt-[-4px] max-w-3xl mx-auto text-center text-xl md:text-2xl opacity-80">
                    {!! $page->short_description !!}
                </h2>
            </header>

            <div class="flex flex-col justify-center items-center py-4">
                <section class="container mx-auto py-6 rounded-lg px-6 sm:px-6 max-w-3xl prose prose-lg lg:prose-xl dark:prose-invert dark:prose-headings:text-slate-300 prose-headings:font-heading prose-headings:leading-tighter prose-headings:tracking-tighter prose-headings:font-bold prose-img:rounded-md prose-img:shadow-lg mt-8 prose-a:text-black/75 dark:prose-a:text-white/90 prose-a:underline prose-a:underline-offset-4 prose-a:decoration-primary-500 hover:prose-a:decoration-primary-600 prose-a:decoration-2 hover:prose-a:decoration-4 hover:prose-a:text-black dark:hover:prose-a:text-white break-words tracking-normal prose-h4:tracking-normal prose-h5:tracking-normal prose-h6:tracking-normal prose-code:before:hidden prose-code:after:hidden markdown-body">
                    {!! str($page->body)->markdown() !!}
                </section>
            </div>
        </section>
    </div>
    @php $skills = \TomatoPHP\FilamentCms\Models\Post::query()
                     ->where('type', 'skill')
                     ->where('is_published', 1)
                     ->get();
    @endphp
    @if(count($skills))
        <div class="bg-white dark:bg-black/20 min-h-screen">
        <section class="container mx-auto">
            <header class="my-6">
                <h1 class="px-4 sm:px-6 max-w-3xl mx-auto text-center text-5xl md:text-6xl font-bold leading-tighter tracking-tighter mb-8 font-heading">
                    {{ trans('cms::messages.about.title') }}
                </h1>
                <h2 class="px-4 sm:px-6 mt-[-4px] max-w-3xl mx-auto text-center text-xl md:text-2xl opacity-80">
                    {{ trans('cms::messages.about.description') }}
                </h2>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">


                @foreach($skills as $skill)
                    <a class=" p-4 bg-white border shadow-sm rounded-xl focus:outline-none focus:ring hover:border-gray-200 hover:ring-1 hover:ring-gray-200" href="{{ $skill->keywords }}" target="_blank">
                        <div class="flex justify-center items-center flex-col">
                            <div class="flex flex-col justify-center items-center p-3 w-12 h-12 text-white rounded-lg bg-main">
                                <x-icon name="{{ $skill->slug }}" class="w-10 h-10"></x-icon>
                            </div>
                            <h6 class="mt-2 font-bold">{!! $skill->title !!}</h6>
                            <p class="hidden sm:mt-1 sm:text-sm sm:text-gray-600 sm:block"> {!! str($skill->body)->markdown() !!} </p>
                        </div>

                        <div class="relative pt-1">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <span class="inline-block px-2 py-1 text-xs font-semibold text-white uppercase rounded-full bg-sec"> Level </span>
                                </div>
                                <div class="text-right">
                                    <span class="inline-block text-xs font-semibold text-sec"> {{ $skill->short_description }}</span>
                                </div>
                            </div>
                            <div class="flex h-2 mb-4 overflow-hidden text-xs rounded bg-secondary">
                                <div class="flex flex-col justify-center text-center text-white shadow-none bg-main whitespace-nowrap" style="width: {{ $skill->short_description }};"></div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    </div>
    @endif
@endsection
