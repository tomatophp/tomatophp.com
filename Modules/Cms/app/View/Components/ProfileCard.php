<?php

namespace Modules\Cms\View\Components;

use App\Models\Account;
use Illuminate\View\Component;
use Illuminate\View\View;

class ProfileCard extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public Account $account
    )
    {
        //
    }

    /**
     * Get the view/contents that represent the component.
     */
    public function render(): View|string
    {
        return view('cms::components.profile-card');
    }
}
