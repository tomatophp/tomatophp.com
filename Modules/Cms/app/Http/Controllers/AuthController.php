<?php

namespace Modules\Cms\Http\Controllers;

use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function login()
    {
        return redirect()->to('/user/login');
    }

    public function register()
    {
        return redirect()->to('/user/register');
    }

    public function otp()
    {
        return view('cms::auth.otp');
    }
}
