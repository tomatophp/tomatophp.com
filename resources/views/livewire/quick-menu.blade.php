<div class="quick-create-component">
    <x-filament::dropdown placement="bottom-end" :teleport="true">
        <x-slot name="trigger">
            <button
                x-tooltip="{
                    content: 'Links',
                    theme: $store.theme,
                }"
                @class([
                    'flex flex-shrink-0 bg-gray-100 items-center justify-center text-primary-500 hover:text-primary-900 dark:bg-gray-800 hover:bg-primary-500 dark:hover:bg-primary-500',
                    'rounded-full',
                    'w-8 h-8'
                ])
            >
                <x-filament::icon
                    icon="heroicon-o-link"
                    class="w-5 h-5"
                />
            </button>
        </x-slot>
        <x-filament::dropdown.list>
            @foreach($resources as $resource)
                <x-filament::dropdown.list.item
                    :icon="$resource['icon']"
                    :href="$resource['url']"
                    target="_blank"
                    tag="a"
                >
                    {{ $resource['label'] }}
                </x-filament::dropdown.list.item>
            @endforeach
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
