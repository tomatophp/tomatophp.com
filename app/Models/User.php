<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravelcm\Subscriptions\Traits\HasPlanSubscriptions;
use Spatie\Permission\Traits\HasRoles;
use TomatoPHP\FilamentAlerts\Traits\InteractsWithNotifications;
use TomatoPHP\FilamentFcm\Traits\InteractsWithFCM;
use TomatoPHP\FilamentSocial\Traits\InteractsWithSocials;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable;
    use HasRoles;
    use InteractsWithNotifications;
    use InteractsWithFCM;
    use HasPlanSubscriptions;
    use InteractsWithSocials;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'packages',
        'username',
        'profile_photo_path'
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'packages' => 'json',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->profile_photo_path;
    }
}
