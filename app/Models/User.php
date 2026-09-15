<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use TomatoPHP\FilamentLanguageSwitcher\Traits\InteractsWithLanguages;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, InteractsWithLanguages, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * The demo and admin accounts of the public demo cannot be changed from the panel.
     */
    public function isDemoProtected(): bool
    {
        if (! config('demo.enabled')) {
            return false;
        }

        return in_array(
            $this->getOriginal('email') ?? $this->email,
            array_filter([config('demo.email'), config('demo.admin.email')]),
            true,
        );
    }

    /**
     * Checked by tomatophp/filament-users before impersonating.
     */
    public function canBeImpersonated(): bool
    {
        return ! $this->isDemoProtected();
    }

    protected static function booted(): void
    {
        // Defense in depth for package actions that skip policies: a signed-in user can
        // never change or delete a protected demo account. Seeders and the CLI run unauthenticated.
        $guard = function (User $user, bool $deleting): void {
            if (! auth()->check() || ! $user->isDemoProtected()) {
                return;
            }

            $changesIdentity = $deleting || $user->isDirty(['name', 'email', 'password']);

            abort_if($changesIdentity, 403, __('Demo accounts cannot be changed.'));
        };

        static::updating(fn (User $user) => $guard($user, false));
        static::deleting(fn (User $user) => $guard($user, true));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
