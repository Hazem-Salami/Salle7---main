<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use SimpleSoftwareIO\QrCode\Facades\QrCode;

Route::get('/profile/user', function () {
//    $path =  getenv('IMAGE_URL').'QrCodes/55_qrcode.png';

    $path = '/QrCodes/';
    $file_path = $path . time() . '.png';
    $png = QrCode::format('png')
        ->color(74, 139, 223)
        ->size(200)->errorCorrection('H') ->generate("id:" . 1);$path = '/QrCodes/';
    if(!\File::exists(public_path($path))) {
        \File::makeDirectory(public_path($path));
    }
    $time = Carbon::now();
    $time = $time->toDateString() . '_' . $time->hour . '_' . $time->minute . '_' . $time->second;
    $path = 'QrCodes/' . $time . '_qrcode.png';
    $renderer = new ImageRenderer(
        new RendererStyle(400),
        new ImagickImageBackEnd()
    );
    $writer = new Writer($renderer);
    $writer->writeFile('Id: ' . 1, $path);
//    \Illuminate\Support\Facades\Storage::disk('local')->put($output_file, $png);
//    $path = '/QrCodes/4_qrcode.png';
//    QrCode::size(400)
//        ->format('png')
////        ->color(74, 139, 223)
//        ->generate("id:" . 1, public_path($path));
});
Route::get('/', function () {
    return view('welcome');
});
