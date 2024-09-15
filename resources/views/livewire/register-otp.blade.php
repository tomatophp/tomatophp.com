<div>
    <div>
        <x-filament::section :heading="__('OTP')" :description="__('Please check our discord server for OTP')">
            <x-filament-panels::form wire:submit.prevent="authenticate">
                {{ $this->form }}

                {{ $this->submitAction }}
            </x-filament-panels::form>
        </x-filament::section>

        <div class="text-center my-4">
            <span class="text-gray-400">Don't get the code? please <span class="underline text-primary-500">{{ $this->getResendAction }}</span></span>
            <x-filament-actions::modals />
        </div>
    </div>
</div>
