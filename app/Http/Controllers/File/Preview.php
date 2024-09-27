<?php

namespace App\Http\Controllers\File;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;

class Preview extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(File $file)
    {
        $filepath = storage_path('app/'.$file->archiveBox->slug.'/'.$file->path);
        return response()->file($filepath);
    }
}
