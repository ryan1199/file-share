<?php

namespace App\Livewire\File;

use App\Events\File\Liked;
use App\Events\File\Viewed;
use App\Models\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Mary\Traits\Toast;

class Show extends Component
{
    use Toast;

    #[Locked]
    public File $file;
    public $likes = 0;
    public $views = 0;
    public $downloads = 0;
    #[Locked]
    public $imageFileExtensions = [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'webp',
        'tiff',
        'bmp',
        'ico',
        'svg',
        'heic',
        'avif',
    ];
    #[Locked]
    public $audioFileExtensions = [
        'mp3',
        'wav',
        'ogg',
        'aac',
        'flac',
        'm4a',
        'wma',
        'alac',
        'aiff',
        'mid',
        'midi',
        'amr',
    ];
    #[Locked]
    public $videoFileExtensions = [
        'mp4',
        'avi',
        'mov',
        'mkv',
        'webm',
        'flv',
        'mpg',
        'mpeg',
        'wmv',
        'rmvb',
        'vob',
        'swf',
        'gif',
        '3gp',
        '3gpp',
        '3g2',
        '3gp2',
    ];

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
        Viewed::dispatch($this->file);
        $this->likes = $this->file->likes->count();
        $this->views = $this->file->views;
        $this->downloads = $this->file->downloads;
    }
    public function like()
    {
        if (Auth::check()) {
            if (!$this->file->likes->contains(Auth::id())) {
                $result = false;
                DB::transaction(function () use (&$result) {
                    $this->file->likes()->attach(Auth::id());
                    $result = true;
                }, attempts: 100);
                if ($result) {
                    $this->toast('success','Liked successfully!', position: 'toast-bottom');
                } else {
                    if (File::find($this->file->id) != null) {
                        $this->toast('error','Failed to like this file.', position: 'toast-bottom');
                    } else {
                        $this->toast('error','Failed to like this file because file does not exists', position: 'toast-bottom');
                    }
                }
            } else {
                $result = false;
                DB::transaction(function () use (&$result) {
                    $this->file->likes()->detach(Auth::id());
                    $result = true;
                }, attempts: 100);
                if ($result) {
                    $this->toast('success','Unliked successfully!', position: 'toast-bottom');
                } else {
                    if (File::find($this->file->id) != null) {
                        $this->toast('error','Failed to unlike this file.', position: 'toast-bottom');
                    } else {
                        $this->toast('error','Failed to unlike this file because file does not exists', position: 'toast-bottom');
                    }
                }
            }
            $this->likes = $this->file->likes()->count();
            $this->file->load('likes');
            Liked::dispatch($this->file);
        } else {
            $this->toast('error','Please login to like this file.', position: 'toast-bottom');
        }
    }
    public function loadLikes()
    {
        $this->likes = $this->file->likes()->count();
        $this->file->load('likes');
    }
    public function loadViews()
    {
        $this->views = $this->file->views;
    }
    public function loadDownloads()
    {
        $this->downloads = $this->file->downloads;
    }
    public function getListeners()
    {
        return [
            "echo:file.show.{$this->file->slug},File\Liked" => 'loadLikes',
            "echo:file.show.{$this->file->slug},File\Viewed" => 'loadViews',
            "echo:file.show.{$this->file->slug},File\Downloaded" => 'loadDownloads',
        ];
    }
}
