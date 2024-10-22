@extends('cms::layouts.app')

@section('title', appTitle(trans('cms::messages.community.label')))
@section('description', appDescription(trans('cms::messages.community.title') . ' ' . trans('cms::messages.community.sub')))

@section('body')
    <div class="bg-slate-50 dark:bg-inherit min-h-screen">
        <section class="container sm:px-6 py-12 sm:py-16 lg:py-20 mx-auto">
            <header>
                <h1 class="text-center text-4xl md:text-6xl font-bold leading-tighter tracking-tighter  font-heading">
                    {{ trans('cms::messages.community.title') }}
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-success-500 to-danger-500 pr-[0.025em] mr-[-0.025em]">
                        {{  trans('cms::messages.community.sub') }}
                    </span>
                </h1>
            </header>
            <div class="flex flex-col justify-center items-center my-4">
                <div class="max-w-md">
                    <x-cms-filter-toolbar />
                </div>
            </div>
            @if(count($accounts))
                <div class="grid grid-cols-1 mx-8 md:grid-cols-3 gap-4">
                    @foreach ($accounts as $account)
                        <li class="flex flex-col gap-4">
                            <x-cms-profile-card :account="$account"/>
                        </li>
                    @endforeach
                </div>

        </section>

        <div>
            <div class="flex flex-col justify-center items-center my-4">
                {!! $accounts->links() !!}
            </div>
        </div>

        @else
            <x-cms-empty-state :name="trans('cms::messages.community.label')"/>
        @endif
    </div>
@endsection
