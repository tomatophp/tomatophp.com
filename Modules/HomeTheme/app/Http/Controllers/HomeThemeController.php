<?php

namespace Modules\HomeTheme\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HomeThemeController extends Controller
{
    public function index()
    {
        $page = load_page('/');
        return view('hometheme::index', ['page'=> $page]);
    }
}
