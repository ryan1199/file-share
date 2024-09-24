<?php

namespace App\Livewire\User\ArchiveBox;

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;

class Index extends Component
{
    use Toast;

    public string $search = '';
    public bool $drawer = false;
    #[Locked]
    public array $availableSortBy = [
        ['id' => 'name','name' => 'Name',],
        ['id' => 'created_at','name' => 'Created At',]
    ];
    public $sortBy = 'name';
    public bool $asc = true;
    #[Locked]
    public User $user;

    public function render()
    {
        return view('livewire.user.archive-box.index', [
            'archiveBoxes' => $this->archiveBoxes(),
        ]);
    }
    public function archiveBoxes()
    {
        $archiveBoxes = $this->user->archiveBoxes()->when($this->search, function ($query) {
            $query->where('name', 'like', '%'.$this->search.'%')->orWhere('slug', 'like', '%'.$this->search.'%')->orWhere('description', 'like', '%'.$this->search.'%');
        })->orderBy($this->sortBy, $this->asc ? 'asc' : 'desc')->take(10)->get();
        $slides = [];
        foreach ($archiveBoxes as $archiveBox) {
            $slides[] = [
                'title' => $archiveBox->name,
                'description' => Str::words($archiveBox->description, 10, ' ...'),
                'image' => asset('storage/covers/'.$archiveBox->cover),
                'url' => route('archive-box.show', $archiveBox->slug),
                'urlText' => $archiveBox->slug,
            ];
        }
        return $slides;
    }
    public function clear(): void
    {
        $this->reset(['search', 'sortBy', 'asc']);
        $this->success('Filters cleared.', position: 'toast-bottom');
    }
    public function mount(User $user)
    {
        $this->user = $user;
    }
}
