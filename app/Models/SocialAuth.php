<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\Tenant;

class SocialAuth extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'provider',
        'provider_id'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
