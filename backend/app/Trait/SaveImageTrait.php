<?php

namespace App\Trait;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait SaveImageTrait
{
        /**
     * save Image.
     */
    private function saveImage(UploadedFile $image,$dir)
    {
        $path ='images/'. $dir .'/'. Str::random();
        if (!Storage::disk('public')->exists($path)) {
            Storage::disk('public')->makeDirectory($path);
        }

        $file_path = $image -> storeAs($path, $image ->getClientOriginalName(),'public');



        if (!$file_path) {
            throw new \Exception("Unable to save file \"{$image->getClientOriginalName()}\"");
        }

        return $file_path;
    }
}
