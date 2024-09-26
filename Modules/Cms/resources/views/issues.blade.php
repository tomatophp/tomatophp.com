@extends('cms::layouts.app')

@section('title', (app()->getLocale() === 'en' ? str(setting('site_name'))->explode('|')[0]??setting('site_name') : str(setting('site_name'))->explode('|')[1]??setting('site_name')) . ' | '. trans('cms::messages.issues.title'))
@section('description', trans('cms::messages.issues.description'))
@section('keywords','issues, tomatophp, cms, laravel')

@section('body')
    <div class="bg-slate-50 dark:bg-inherit min-h-screen">
        <section class="container sm:px-6 py-12 sm:py-16 lg:py-20 mx-auto">
            <header>
                <h1 class="px-4 sm:px-6 max-w-3xl mx-auto text-center text-5xl md:text-6xl font-bold leading-tighter tracking-tighter mb-8 font-heading">
                    {{ trans('cms::messages.issues.title') }}
                </h1>
                <h2 class="px-4 sm:px-6 mt-[-4px] max-w-3xl mx-auto text-center text-xl md:text-2xl opacity-80">
                    {{ trans('cms::messages.issues.description') }}
                </h2>
            </header>

            <div class="flex flex-col justify-center items-center py-4">
                @livewire(\App\Livewire\ListIssues::class)
            </div>
        </section>
    </div>
@endsection
