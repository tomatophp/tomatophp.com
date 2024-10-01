<?php

namespace Modules\Cms\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UserMenu extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(

    )
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('cms::components.user-menu');
    }
}
