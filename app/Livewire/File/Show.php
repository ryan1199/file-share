<?php

namespace App\Livewire\File;

use App\Models\File;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mary\Traits\Toast;

class Show extends Component
{
    use Toast;

    public File $file;
    public $likes = 0;
    public $views = 0;
    public $downloads = 0;

    public function render()
    {
        return view('livewire.file.show');
    }
    public function mount(File $file)
    {
        $this->file = $file;
        $this->file->load('archiveBox');
        $this->file->load('likes');
        if (Auth::check()) {
            $this->file->update([
                'views' => $this->file->views + 1,
            ]);
        }
        $this->likes = $this->file->likes->count();
        $this->views = $this->file->views;
        $this->downloads = $this->file->downloads;
    }
    public function like()
    {
        if (Auth::check()) {
            if (!$this->file->likes->contains(Auth::id())) {
                $this->file->likes()->attach(Auth::id());
                $this->toast('success','Liked successfully!', position: 'toast-bottom');
            } else {
                $this->file->likes()->detach(Auth::id());
                $this->toast('success','Unliked successfully!', position: 'toast-bottom');
            }
            $this->likes = $this->file->likes()->count();
            $this->file->load('likes');
        } else {
            $this->toast('error','Please login to like this file.', position: 'toast-bottom');
        }
    }
}
