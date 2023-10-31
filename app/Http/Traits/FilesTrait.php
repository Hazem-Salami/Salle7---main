<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\ArrayShape;

trait FilesTrait
{
    public function storeFile($file, $string, $counter = null): string
    {
        $extension = $file->getClientOriginalExtension();
//        $type = $this->getMediaType($extension);
        $time = Carbon::now();
        $time = $time->toDateString() . '_' . $time->hour . '_' . $time->minute . '_' . $time->second;

        if ($counter != null) {
            $name = $time . '_' . $string . '_' . $counter . '.' . $extension;
        } else {

            $name = $time . '_' . $string . '.' . $extension;
        }
        $disk = Storage::build([
            'driver' => 'local',
            'root' =>   $string.'/',
        ]);

        $disk->put($name, file_get_contents($file->path()));
        $path = '/' . $string . '/' . $name;

        return $path;
    }

    public function destoryFile($path): bool
    {
        if($path) {
            $disk = Storage::build([
                'driver' => 'local',
                'root' => './'
            ]);
            $disk->delete($path);
            return true;
        }
        return false;
    }
//    private function getMediaType($extension)
//    {
//        if ($this->is_document($extension)) {
//
//            $directory = 'image_';
//            $folder = 'images';
//            $type = 1;
//        }
//
//        $response = [
//            'directory' => $directory,
//            'folder' => $folder,
//            'type' => $type,
//        ];
//        return $response;
//    }
//
//    private function is_document($extension): bool
//    {
//        if ($extension == 'pdf' || $extension == 'docx')
//            return true;
//        return false;
//    }
}
