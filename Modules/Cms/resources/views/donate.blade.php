@extends('cms::layouts.app')

@section('title', appTitle($page->title))
@section('description', appDescription($page->short_description))
@section('keywords', appKeywords($page->keywords))
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
@endsection
