<?php

namespace App\Http\Controllers\File;

use App\Events\File\Downloaded;
use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Download extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(File $file)
    {
        $result = false;
        DB::transaction(function () use (&$result, $file) {
            $file->update([
                'downloads' => $file->downloads + 1,
            ]);
            $result = true;
        }, attempts: 100);
        if ($result) {
            Downloaded::dispatch($file);
            $filepath = storage_path('app/'.$file->archiveBox->slug.'/'.$file->path);
            $name = $file->name.'.'.$file->extension;
            return response()->download($filepath, name: $name);
        } else {
            abort(503, 'Unable to download file: '.$file->name.' try again later');
        }
    }
}
