<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Support\Facades\Storage;

trait Base64Trait
{
    public function base64Encode($file): string
    {
        return base64_encode(file_get_contents($file->path()));
    }

    public function base64Decode($base64encode): string
    {
        return base64_decode($base64encode);
    }

    public function saveFile($extension ,$base64decode ,$string ,$counter = null): string
    {
        $time = Carbon::now();
        $time = $time->toDateString() . '_' . $time->hour . '_' . $time->minute . '_' . $time->second;

        if ($counter != null) {
            $name = $time . '_' . $string . '_' . $counter . '.' . $extension;

        } else {
            $name = $time . '_' . $string . '.' . $extension;
        }

        $disk = Storage::build([
            'driver' => 'local',
            'root' =>   'public/'.$string.'/',
        ]);

        $disk->put($name, $base64decode);

        $path = '/' . $string . '/' . $name;

        return $path;
    }

    public function deleteFile($path): bool
    {
        if($path) {
            $disk = Storage::build([
                'driver' => 'local',
                'root' => './public'
            ]);
            $disk->delete($path);
            return true;
        }
        return false;
    }

}
