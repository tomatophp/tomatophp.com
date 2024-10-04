@extends('cms::layouts.app')

@section('title', (app()->getLocale() === 'en' ? str(setting('site_name'))->explode('|')[0]??setting('site_name') : str(setting('site_name'))->explode('|')[1]??setting('site_name')) . ' | '.$account->name)
@section('description', $account->meta('bio'))
@section('keywords', $account->meta('bio'))
@if($account->getFirstMediaUrl('avatar'))
    @section('image', $account->getFirstMediaUrl('avatar'))
@endif

@section('body')
    <div class="h-full min-h-screen">
        <div class="h-[150px] lg:h-[350px] bg-slate-300 dark:bg-slate-700 bg-cover border-b border-slate-200 dark:border-slate-700">
            <div class="flex flex-col justify-center items-center text-center h-full">
                <img src="{{ $account->getFirstMediaUrl('cover') ?: url('cover.png') }}" class="w-full h-full bg-cover bg-center object-cover" alt="cover">
            </div>
        </div>
        <div>
            <div>
                <div class="flex justify-center flex-col items-center -mt-24">
                    <div class="w-32 h-32 rounded-full bg-slate-200 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                        <x-filament-panels::avatar.user :user="$account" size="32" class="w-32 h-32 rounded-full object-cover"/>
                    </div>
                </div>
                <div class="text-center flex flex-col mt-4 ">
                    <div class="flex justify-center gap-2 font-bold">
                        <a href="{{ url('/@' .$account->username) }}" class="text-2xl"> {{ $account->name }} </a>
                        <div class="flex flex-col justify-center items-center">
                            @if($account->type === 'verified')
                                <x-filament::icon-button icon="bxs-badge-check" tooltip="Verified User" color="info">
                                    <x-icon name="bxs-badge-check" class="text-blue-400 text-xl w-5 h-5" />
                                </x-filament::icon-button>
                            @elseif($account->type === 'public')
                                <x-filament::icon-button icon="bxs-badge-check" tooltip="Public User">
                                    <x-icon name="bxs-badge-check" class="text-blue-400 text-xl w-5 h-5" />
                                </x-filament::icon-button>
                            @endif
                        </div>
                    </div>
                    <div class="flex justify-center gap-2">
                        <h6 class="text-sm font-medium text-slate-500 dark:text-slate-300">{{ '@'.$account->username }}</h6>
                    </div>
                    @if(!empty($account->meta('bio')))
                        <p class="text-xs text-center my-2 mx-2">
                            {{ $account->meta('bio') }}
                        </p>
                    @endif
                    @if(!empty($account->meta('location')))
                        <div class="flex justify-center gap-2">
                            <div class="flex flex-col justify-center items-center">
                                <x-icon name="bxs-map" class="mt-1 text-primary-600 w-4 h-4" />
                            </div>
                            <div>
                                <h1 class="text-slate-500 dark:text-slate-300"> {{ $account->meta('location') }} </h1>
                            </div>
                        </div>
                    @endif
                    @if($account->meta('social') && is_array($account->meta('social')))
                    <div class="flex flex-wrap justify-center items-center my-4 gap-4">
                        @foreach($account->meta('social') as $item)
                            <a href="{{  $item['url'] }}" target="_blank">
                                @if($item['network'] === 'link')
                                    <x-icon name="heroicon-s-link" class="w-5 h-5" />
                                @else
                                    <x-icon :name="'bxl-'.$item['network']" class="w-5 h-5" />
                                @endif

                            </a>
                        @endforeach
                    </div>
                    @endif
                    <h6 class="my-2 text-sm text-slate-500 dark:text-slate-300">
                        Joined {{ $account->created_at->diffForHumans() }}
                    </h6>
                </div>
            </div>
            <div class="flex flex-col justify-center items-center mx-8 md:mx-16 mt-8">
                <div class="w-full md:w-3/4 lg:w-1/2">
                    <div class="flex justify-start text-start">
                        <h1>Last Activity</h1>
                    </div>
                    @php $comments = \TomatoPHP\FilamentCms\Models\Comment::query()->where('user_id', $account->id)->where('user_type', \App\Models\Account::class)->paginate(10); @endphp
                    @if(count($comments))
                        <div class="grid grid-cols-1 gap-4 my-4">
                            @foreach($comments as $comment)
                                <div class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm mt-4 overflow-hidden">
                                    <div class="flex justify-between w-full border-b border-slate-200 dark:border-slate-700 p-4">
                                        <div class="w-full flex justify-between gap-2">
                                            <div class="w-full flex justify-start gap-2">
                                                <a href="{{ url(app()->getLocale() . '/@' . $account->username) }}">
                                                    <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-200 dark:border-slate-700">
                                                        <x-filament-panels::avatar.user :user="$account" size="8"/>
                                                    </div>
                                                </a>
                                                <a href="{{ url(app()->getLocale() . '/' . ($comment->content?->type === 'open-source' ? 'open-source' : 'blog') . '/' . $comment->content?->slug) }}" target="_blank" class="flex flex-col justify-center">
                                                    <div class="flex justify-start gap-2">
                                                        <div>
                                                            <h1 class="font-bold text-md">{{ $comment->content?->title }}</h1>
                                                        </div>
                                                        <div class="flex flex-col justify-center items-center">
                                                            <x-icon name="bx-link-external" class="w-4 h-4"/>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div>
                                                <div class="flex flex-col justify-center items-center">
                                                    <x-icon name="heroicon-o-chat-bubble-left-right" class="w-5 h-5"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-center">
                                        <div class="flex flex-col justify-center w-full p-4">
                                            <section data-theme="light" class="prose prose-lg lg:prose-xl dark:prose-invert dark:prose-headings:text-slate-300 prose-headings:font-heading prose-headings:leading-tighter prose-headings:tracking-tighter prose-headings:font-bold prose-img:rounded-md prose-img:shadow-lg prose-a:text-black/75 dark:prose-a:text-white/90 prose-a:underline prose-a:underline-offset-4 prose-a:decoration-primary-500 hover:prose-a:decoration-primary-600 prose-a:decoration-2 hover:prose-a:decoration-4 hover:prose-a:text-black dark:hover:prose-a:text-white break-words tracking-normal prose-h4:tracking-normal prose-h5:tracking-normal prose-h6:tracking-normal prose-code:before:hidden prose-code:after:hidden markdown-body">
                                                {!! str($comment->comment)->markdown() !!}
                                            </section>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mx-auto my-4">
                            {{ $comments->links() }}
                        </div>
                    @else
                        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 mx-8 md:mx-0 mt-6 mb-8 rounded-lg shadow-sm flex justify-center">
                            <div class="p-8 md:p-16 text-center">
                                <i class="bx bx-x-circle bx-lg text-danger-500"></i>
                                <h1>This Profile is empty!</h1>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            <x-cms-social-share />
        </div>
    </div>
@endsection
