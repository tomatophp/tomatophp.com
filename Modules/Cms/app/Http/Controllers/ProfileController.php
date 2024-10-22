<?php

namespace Modules\Cms\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use TomatoPHP\FilamentCms\Models\Post;

class ProfileController extends Controller
{
    public function index($username)
    {
        $account = Account::query()->where('username', str($username)->remove('@')->toString())->first();
        if($account && (bool)$account->meta('is_public')){
            return view('cms::profile.index', compact('account'));
        }
        else {
            $page = Post::query()->where('type', 'page')->where('slug', $username)->first();

            if($page){
                $page->views += 1;
                $page->save();

                return view('cms::page', [
                    'page' => $page
                ]);
            }
            else {
                abort(404);
            }
        }

    }
}
