<?php

namespace App\Filament\Apps\Response;

use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Symfony\Component\HttpFoundation\Response;

class RegisterResponse implements RegistrationResponse
{
    /**
     * @param $request
     * @return RedirectResponse|Response|Redirector
     */
    public function toResponse($request): RedirectResponse|Response|Redirector
    {
        return redirect()->route(app()->getLocale() . '.auth.otp');
    }
}
