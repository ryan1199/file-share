<?php

namespace App\Livewire;

use App\Models\ArchiveBox;
use Livewire\Component;
use Mary\Traits\Toast;

class Home extends Component
{
    use Toast;

    public function render()
    {
        return view('livewire.home', [
            'archiveBoxes' => $this->archiveBoxes(),
        ]);
    }
    public function archiveBoxes()
    {
        return ArchiveBox::query()
        ->withCount('users')
        ->withCount('files')
        ->having('users_count', '>', 10)
        ->orderBy('users_count', 'asc')
        ->take(10)
        ->get();
    }
}
