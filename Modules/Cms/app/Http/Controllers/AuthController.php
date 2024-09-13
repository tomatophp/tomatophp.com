<?php

namespace Modules\Cms\Http\Controllers;

use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function login()
    {
        return view('cms::auth.login');
    }

    public function register()
    {
        return view('cms::auth.register');
    }

    public function otp()
    {
        return view('cms::auth.otp');
    }
}
