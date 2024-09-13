@extends('cms::layouts.app')

@section('title', (app()->getLocale() === 'en' ? str(setting('site_name'))->explode('|')[0]??setting('site_name') : str(setting('site_name'))->explode('|')[1]??setting('site_name')) . ' | '. 'Login')
@section('description', 'Login To TomatoPHP Demo')
@section('keywords', 'tomatophp, demo, login page')

@section('body')
    <section class="h-screen flex flex-col justify-center items-center">
        <div class="p-4 w-full lg:w-1/3">
            <div class="flex flex-col justify-center items-center my-4">
                <x-application-logo class="w-16 h-16" />
            </div>
            @livewire(\App\Livewire\Login::class)
        </div>
    </section>
@endsection
