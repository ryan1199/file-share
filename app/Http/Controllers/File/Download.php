<?php

namespace App\Http\Controllers\File;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;

class Download extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(File $file)
    {
        $file->update([
            'downloads' => $file->downloads + 1,
        ]);
        $filepath = storage_path('app/'.$file->archiveBox->slug.'/'.$file->path);
        $name = $file->name.'.'.$file->extension;
        return response()->download($filepath, name: $name);
    }
}
