<x-filament-widgets::widget>
    <x-filament::section>
       <a href="{{ url('user/edit-profile') }}">
           <div class="flex justify-between gap-4 text-custom-500" style="--c-50:var(--warning-50);--c-400:var(--warning-400);--c-500:var(--warning-500);">
               <div>
                   <h1  class="text-xl ">Your <b>Public Profile</b> is not active, go to your Edit profile page to active it?</h1>
               </div>
               <div>
                   <x-icon name="heroicon-s-user" class="w-6 h-6" ></x-icon>
               </div>
           </div>
       </a>
    </x-filament::section>
</x-filament-widgets::widget>
