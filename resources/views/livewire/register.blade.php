<div>
    <div>
        {{ $this->form }}

        <div class="my-4 flex flex-col justify-center gap-4">
            <x-cms-main-button type="button" wire:click="submit()" :label="__('Register')" icon="bxs-user" />
            <div class="text-center">
                Have account? please <a href="{{ url(app()->getLocale() . '/login') }}" class="text-primary-600 hover:text-primary-800">Login</a>
            </div>
        </div>
    </div>
</div>
