@extends('cms::layouts.app')

@section('title', (app()->getLocale() === 'en' ? str(setting('site_name'))->explode('|')[0]??setting('site_name') : str(setting('site_name'))->explode('|')[1]??setting('site_name')) . ' | '.$account->name)
@section('description', $account->short_description)
@section('keywords', $account->keywords)
@if($account->getFirstMediaUrl('avatar'))
    @section('image', $account->getFirstMediaUrl('avatar'))
@endif

@section('body')
    <div class="h-full min-h-screen">
        <div class="h-[150px] lg:h-[350px] bg-slate-700 bg-cover border-b border-slate-700">
            <div class="flex flex-col justify-center items-center text-center h-full">
                <img src="{{ $account->getFirstMediaUrl('cover') }}" class="w-full h-full bg-cover bg-center object-cover" alt="cover">
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3">
            <div class="justify-start gap-4 mt-8 mx-16 hidden lg:flex"></div>
            <div>
                <div class="flex justify-center flex-col items-center -mt-24">
                    <div class="w-32 h-32 rounded-full bg-slate-800 border border-slate-700">
                        <x-filament-panels::avatar.user :user="$account" size="32" class="w-32 h-32 rounded-full object-cover"/>
                    </div>
                </div>
                <div class="text-center flex flex-col mt-4">
                    <div class="flex justify-center gap-2 font-bold">
                        <a href="{{ url(app()->getLocale() . '/' .$account->username) }}" class="text-2xl"> {{ $account->name }} </a>
                    </div>
                    <div class="flex justify-center gap-2">
                        <h6 class="text-sm font-medium text-slate-300">{{ '@'.$account->username }}</h6>
                    </div>
                    <h6 class="my-2 text-sm text-slate-300">
                        Joined {{ $account->created_at->diffForHumans() }}
                    </h6>
                </div>
            </div>
            <div class="flex justify-center md:justify-end gap-4 mt-8 mx-16">

            </div>
            <div class="justify-center md:justify-start gap-4 my-8 mx-16 flex lg:hidden"></div>
        </div>
    </div>
@endsection
