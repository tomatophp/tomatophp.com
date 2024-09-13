@extends('cms::layouts.app')

@section('title', (app()->getLocale() === 'en' ? str(setting('site_name'))->explode('|')[0]??setting('site_name') : str(setting('site_name'))->explode('|')[1]??setting('site_name')) . ' | '. 'Register')
@section('description', 'Register To TomatoPHP Demo')
@section('keywords', 'tomatophp, demo, login page')

@section('body')
    <section class="h-full flex flex-col justify-center items-center my-16">
        <div class="p-4 w-full lg:w-1/3">
            <div class="flex flex-col justify-center items-center my-4">
                <x-application-logo class="w-16 h-16" />
            </div>
            @livewire(\App\Livewire\Register::class)
        </div>
    </section>
@endsection
