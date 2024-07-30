<?php

namespace App\Livewire;

use Livewire\Component;

class QuickMenu extends Component
{
    public array $resources = [];

    public function mount()
    {
        $this->resources = [
            [
                'label' => 'Docs',
                'icon' => 'bxs-file-doc',
                'url' => 'https://docs.tomatophp.com'
            ],
            [
                'label' => 'Github',
                'icon' => 'bxl-github',
                'url' => 'https://www.github.com/tomatophp'
            ],
            [
                'label' => 'Discord',
                'icon' => 'bxl-discord',
                'url' => 'https://discord.gg/vKV9U7gD3c'
            ],
            [
                'label' => 'Buy Me a Coffee',
                'icon' => 'bxs-coffee',
                'url' => 'https://github.com/sponsors/3x1io'
            ]


        ];
    }

    public function render()
    {
        return view('livewire.quick-menu');
    }
}
