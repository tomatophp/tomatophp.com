<?php
namespace Modules\Core;
use Services\Core;
use Support\Framework;

if (!function_exists('Modules\\Core\\framework')) {
    function framework(): Framework
    {
        return app(Framework::class);
    }
}

if (!function_exists('Modules\\Core\\core')) {
    function core(): Core
    {
        return app(Core::class);
    }
}

