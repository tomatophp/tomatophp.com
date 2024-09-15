<?php

namespace App\Models;


use App\Models\SocialAuth;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;


class Tenant extends \Stancl\Tenancy\Database\Models\Tenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $casts = [
        'packages' => 'json',
    ];

    protected $hidden = [
        'password',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'phone',
            'password',
            'packages',
        ];
    }

    public function social()
    {
        return $this->hasMany(SocialAuth::class, 'tenant_id', 'id');
    }
}
