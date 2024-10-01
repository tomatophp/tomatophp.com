<?php

namespace Modules\Cms\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Account;

class ProfileController extends Controller
{
    public function index($username)
    {
        $account = Account::query()->where('username', str($username)->remove('@')->toString())->first();
        if($account && (bool)$account->meta('is_public')){
            return view('cms::profile.index', compact('account'));
        }
        else {
            abort(404);
        }

    }
}
