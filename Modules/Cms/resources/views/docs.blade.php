@extends('cms::layouts.app')

@section('title', (app()->getLocale() === 'en' ? str(setting('site_name'))->explode('|')[0]??setting('site_name') : str(setting('site_name'))->explode('|')[1]??setting('site_name')) . ' | '. $docs->title)
@section('description', $docs->short_description)
@section('keywords', $docs->keywords)
@if($docs->getFirstMediaUrl('feature_image'))
    @section('image', $docs->getFirstMediaUrl('feature_image'))
@endif

@section('body')
    <div class="bg-slate-50 dark:bg-inherit min-h-screen">
        <header class="flex flex-col items-center text-center px-4 sm:px-6 mb-4">
            <img src="{{ $docs->getFirstMediaUrl('feature_image') }}" width="224" height="224" class="p-[20px] mb-3">
            <h1 class="text-5xl md:text-6xl font-bold leading-tighter tracking-tighter mb-4 font-heading">
                {!! $docs->title !!}
            </h1>
            <h2 class="text-2xl md:text-3xl tracking-tight mb-8">
                {!! $docs->short_description !!}
            </h2>
            <nav class="flex flex-col sm:flex-row gap-x-4 gap-y-8 mt-2 instapaper_ignore">
                <div class="relative">
                    <x-cms-sub-button icon="bxl-github" :away="true" label="Download" url="{{ $docs->meta_url }}" />
                </div>
                <div class="relative">
                    <x-cms-main-button icon="bxs-download" :away="true" label="{{ number_format($docs->meta('downloads_total'))??0 }}" url="{{ $docs->meta_url }}" />
                </div>
                <div class="relative">
                    <x-cms-main-button icon="bxs-star" :away="true" label="{{ number_format($docs->meta('github_starts'))??0 }}" url="{{ $docs->meta_url }}" />
                </div>
            </nav>
        </header>

        <div class="flex flex-col justify-center items-center py-4">
            <section data-theme="light" class="container mx-auto py-6 rounded-lg px-6 sm:px-6 max-w-3xl prose prose-lg lg:prose-xl dark:prose-invert dark:prose-headings:text-slate-300 prose-headings:font-heading prose-headings:leading-tighter prose-headings:tracking-tighter prose-headings:font-bold prose-img:rounded-md prose-img:shadow-lg mt-8 prose-a:text-black/75 dark:prose-a:text-white/90 prose-a:underline prose-a:underline-offset-4 prose-a:decoration-primary-500 hover:prose-a:decoration-primary-600 prose-a:decoration-2 hover:prose-a:decoration-4 hover:prose-a:text-black dark:hover:prose-a:text-white break-words tracking-normal prose-h4:tracking-normal prose-h5:tracking-normal prose-h6:tracking-normal prose-code:before:hidden prose-code:after:hidden markdown-body">
                {!! str($docs->body)->markdown() !!}
            </section>

            <x-cms-social-share />
            <div>
                @livewire(\Modules\Cms\Livewire\LikePost::class, ['post' => $docs])
            </div>
        </div>
    </div>
@endsection
