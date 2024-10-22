@extends('cms::layouts.app')

@php
    $title = (app()->getLocale() === 'en' ? str(setting('site_name'))->explode('|')[0]??setting('site_name') : str(setting('site_name'))->explode('|')[1]??setting('site_name')) . ' | '. trans('cms::messages.open-source.label');
    $description = trans('cms::messages.open-source.title') . ' ' . trans('cms::messages.open-source.sub');
@endphp
@section('title', isset($docs) ? $docs->title : $title)
@section('description', isset($docs) ? $docs->short_description : $description)
@section('keywords', isset($docs) ? $docs->keywords : setting('site_keywords'))
@if(isset($docs) && $docs->getFirstMediaUrl('feature_image'))
    @section('image', $docs->getFirstMediaUrl('feature_image'))
@endif

@section('body')
    <div class="bg-slate-50 dark:bg-inherit min-h-screen">
        <div class="flex justify-between gap-2">
            <div class="justify-end gap-2 w-full hidden md:flex h-screen overflow-y-auto px-4">
                <div class="flex flex-col justify-start gap-1 py-16 fixed">
                    <form  method="GET" id="filter-form" action="{{ url(app()->getLocale() .'/open-source') }}" class="mb-4">
                        <label class="sr-only" for="search"> {{ trans('cms::messages.filters.search') }} </label>
                        <div class="relative group">
                      <span class="absolute inset-y-0 left-0 flex items-center justify-center w-10 h-10 text-gray-400 transition pointer-events-none group-focus-within:text-primary-600">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                      </span>
                            <input
                                id="search"
                                value="{{ request()->get('search') }}"
                                placeholder="{{ trans('cms::messages.filters.search-placeholder') }}"
                                name="search"
                                type="search"
                                class="block h-10 ltr:pl-8 placeholder-gray-400 dark:bg-gray-900 dark:border-gray-700 transition duration-75 border-gray-300 rounded-lg shadow-sm focus:border-primary-600 focus:ring-1 focus:ring-inset focus:ring-primary-600">
                        </div>
                    </form>
                    @foreach($openSources as $item)
                        @php $getUrl = url( app()->getLocale() .'/open-source/'. $item->slug) @endphp
                        <a href="{{ $getUrl }}" class="px-3 py-1 rounded-lg @if(str(url()->current())->contains($getUrl)) bg-slate-100 dark:bg-slate-800 text-primary-500 @else hover:bg-slate-200 dark:hover:bg-slate-800 @endif">
                            {{ $item->title }}
                        </a>
                    @endforeach
                </div>
            </div>
            @if(!isset($docs) && count($openSources) === 0)
                <div class="w-full">
                    <x-cms-empty-state :name="trans('cms::messages.open-source.label')"/>
                </div>
            @else
                <div>
                    @php
                        $docs = !isset($docs) ? $openSources[0] : $docs;
                    @endphp
                    <div class="flex flex-col justify-center items-center">
                        <section data-theme="light" class="scroll-smooth focus:scroll-auto mx-auto rounded-lg px-6 sm:px-6  prose prose-lg lg:prose-xl dark:prose-invert dark:prose-headings:text-slate-300 prose-headings:font-heading prose-headings:leading-tighter prose-headings:tracking-tighter prose-headings:font-bold prose-img:rounded-md prose-img:shadow-lg mt-8 prose-a:text-black/75 dark:prose-a:text-white/90 prose-a:underline prose-a:underline-offset-4 prose-a:decoration-primary-500 hover:prose-a:decoration-primary-600 prose-a:decoration-2 hover:prose-a:decoration-4 hover:prose-a:text-black dark:hover:prose-a:text-white break-words tracking-normal prose-h4:tracking-normal prose-h5:tracking-normal prose-h6:tracking-normal prose-code:before:hidden prose-code:after:hidden markdown-body">
                            <x-markdown theme="github-dark">
                                {!! $docs->body !!}
                            </x-markdown>

                            <div class="flex flex-col sm:flex-row sm:justify-center gap-2 border-t border-slate-200 dark:border-slate-800">
                                <x-cms-social-share />
                            </div>
                        </section>
                    </div>

                    @livewire(\Modules\Cms\Livewire\CommentPost::class, ['post' => $docs])
                </div>
                <div class="justify-start gap-2 w-full hidden md:flex h-screen overflow-y-auto px-4">
                    <div class="flex flex-col justify-start  py-16 fixed h-full ">
                        @php $filterMenu = str($docs->body)->explode('##'); @endphp
                        @foreach($filterMenu as $key=>$filterItem)
                            @if($key > 1)
                                @php $titleOfMenu = str($filterItem)->explode("\n")[0] @endphp
                                <a href="#{{ str($titleOfMenu)->slug() }}" class="text-sm text-slate-400 border-l px-2 border-slate-200 dark:border-slate-800 py-1">
                                    {{ str($titleOfMenu)->remove('#') }}
                                </a>
                            @endif
                        @endforeach

                        <div class="flex flex-col justify-start items-start">
                            @livewire(\Modules\Cms\Livewire\LikePost::class, ['post' => $docs])
                        </div>


                        <a href="{{ $docs->meta_url . "/blob/master/README.md" }}" target="_blank" class="flex justify-start gap-2 text-sm text-slate-400 ">
                            <div class="flex flex-col justify-center items-center">
                                <x-icon name="bxl-github" class="w-5 h-5" />
                            </div>
                            <div>
                                Edit this page on GitHub
                            </div>
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection


@push('css')
    <link rel="stylesheet" href="{{ asset('css/code/github-dark.min.css') }}">
    <style>
        .hljs {
            border: transparent;
            border-radius: 10px;
        }
    </style>
@endpush

@push('js')
    @if(!isset($docs))
        <script>
            const dropdown = document.getElementById("sort");

            dropdown.addEventListener("change", function() {
                document.getElementById("filter-form")?.submit();
            });
        </script>
    @endif


    <script src="{{ asset('js/highlight.min.js') }}"></script>
    <script>hljs.highlightAll();</script>
    <script>
        $(function(){  // $(document).ready shorthand
// get the list of all highlight code blocks
            const highlights = document.querySelectorAll("pre code");

            highlights.forEach((pre) => {
                let addNew = true;
                for(let i=0; i<pre.children.length; i++){
                    if(pre.children[i].innerHTML === 'Copy'){
                        addNew = false;
                    }
                }
                if(addNew){
                    // create the copy button
                    const copy = document.createElement("button");
                    copy.innerHTML = "Copy";

                    copy.classList.add("relative");
                    copy.classList.add("float-right");
                    copy.classList.add("text-xs");
                    copy.classList.add("font-semibold");
                    copy.classList.add("text-white");
                    copy.classList.add("bg-gray-800");
                    copy.classList.add("hover:bg-gray-600");
                    copy.classList.add("rounded-md");
                    copy.classList.add("px-2");
                    copy.classList.add("py-1");
                    copy.classList.add("mt-1");
                    // add the event listener to each click
                    copy.addEventListener("click", handleCopyClick);
                    // append the copy button to each code block
                    pre.prepend(copy);
                    pre.classList.add('hljs');
                }

            });

            const copyToClipboard = (str) => {
                const el = document.createElement("textarea"); // Create a <textarea> element
                el.value = str; // Set its value to the string that you want copied
                el.setAttribute("readonly", ""); // Make it readonly to be tamper-proof
                el.style.position = "absolute";
                el.style.left = "-9999px"; // Move outside the screen to make it invisible
                document.body.appendChild(el); // Append the <textarea> element to the HTML document
                const selected =
                    document.getSelection().rangeCount > 0 // Check if there is any content selected previously
                        ? document.getSelection().getRangeAt(0) // Store selection if found
                        : false; // Mark as false to know no selection existed before
                el.select(); // Select the <textarea> content
                document.execCommand("copy"); // Copy - only works as a result of a user action (e.g. click events)
                document.body.removeChild(el); // Remove the <textarea> element
                if (selected) {
                    // If a selection existed before copying
                    document.getSelection().removeAllRanges(); // Unselect everything on the HTML document
                    document.getSelection().addRange(selected); // Restore the original selection
                }
            };

            function handleCopyClick(evt) {
                // get the children of the parent element
                let innerText = evt.target.parentElement.textContent.replace('Copy', '');

                // copy all of the code to the clipboard
                copyToClipboard(innerText);
                // Change the button's text to "Copied"
                this.textContent = "Copied";
                // Change back the button text to "Copy" after 2 sec
                setTimeout(() => {
                    this.textContent = "Copy";
                }, 2000);
            }
        });
    </script>
@endpush

