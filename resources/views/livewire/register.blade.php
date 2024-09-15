<div>
    <div>
        {{ $this->form }}

        <div class="my-4 flex flex-col justify-center gap-4">
            <x-cms-main-button type="button" wire:click="submit()" :label="trans('cms::messages.register.submit')" icon="bxs-user" />
            <div class="text-center">
                {{ trans('cms::messages.register.already') }} <a href="{{ url(app()->getLocale() . '/login') }}" class="text-primary-600 hover:text-primary-800">{{ trans('cms::messages.register.login') }}</a>
            </div>
        </div>
    </div>
</div>
