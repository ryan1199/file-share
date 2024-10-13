<?php

namespace App\Livewire;

use Livewire\Component;
use Mary\Traits\Toast;

class About extends Component
{
    use Toast;

    public function render()
    {
        return view('livewire.about')->title('About');
    }
}
