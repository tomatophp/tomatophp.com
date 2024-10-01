@extends('cms::layouts.app')

@section('body')
    <div class="flex flex-col justify-center items-center h-screen w-screen gap-4">
        <x-application-logo />
        <h2 class="text-3xl font-bold">Server Error (500)</h2>
        <p class="text-lg text-gray-400">You can request access from <x-filament::link color="danger" href="https://discord.gg/vKV9U7gD3c" target="_blank">discord server</x-filament::link> or use your social media login and select the plugin you went from get stared form</p>
        <div class="flex justify-center gap-4 my-2">
            <x-filament::link href="{{ url(app()->getLocale() . '/') }}">Home</x-filament::link>
            <x-filament::link href="{{ url(app()->getLocale() . '/open-source') }}" target="_blank">Docs</x-filament::link>
            <x-filament::link href="https://www.github.com/tomatophp" target="_blank">Github</x-filament::link>
            <x-filament::link href="https://discord.gg/vKV9U7gD3c" target="_blank">Support</x-filament::link>
            <x-filament::link href="https://github.com/sponsors/3x1io" target="_blank">Buy Me a Coffee</x-filament::link>
        </div>
    </div>
@endsection
