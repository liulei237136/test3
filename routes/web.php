<?php

use App\Http\Controllers\MediaController;
use App\Http\Controllers\PackageAudioController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\SearchController;
use App\Http\Resources\AudioResource;
use App\Models\Package;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if(auth()->user()){
        return Redirect::route('dashboard');
    }
    return Inertia::render('Index');
});

// Route::get('/', [SearchController::class, 'index'])->name('package.index');
Route::get('/packages/{package}/info', [PackageController::class, 'info'])->name('package.info');
Route::get('/packages/{package}/audio', [PackageController::class, 'audio'])->name('package.audio');
Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', function(){
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/media', [MediaController::class, 'index'])->name('media.index');
    Route::post('/media', [MediaController::class, 'store'])->name('media.store');

    Route::get('/packages/create', [PackageController::class, 'create'])->name('package.create');
    Route::get('/packages/{package}/init', [PackageController::class, 'init'])->name('package.init');
    Route::post('/packages', [PackageController::class, 'store'])->name('package.store');
    Route::patch('/packages/{package}', [PackageController::class, 'update'])->name('package.update');
    Route::post('/packages/{package}/audio', [PackageAudioController::class, 'store'])->name('package.audio.store');
    Route::post('/packages/{package}/audio/init_upload', [PackageAudioController::class, 'initUpload'])->name('package.audio.init_upload');
    Route::delete('/packages/{package}/audio/{audio}', [PackageAudioController::class, 'destroy'])->name('package.audio.destroy');
    Route::patch('/packages/{package}/audio/{audio}', [PackageAudioController::class, 'update'])->name('package.audio.update');

    Route::post('/packages/{package}/clone', [PackageController::class, 'clone'] )->name('package.clone');

    Route::post('/packages/{package}/toggle_favorite', [PackageController::class, 'toggleFavorite'])->name('package.toggle_favorite');
});

    Route::get('/test/{package}', function($package){
        $p = Package::without('audio')->findOrFail($package);
        $audio = AudioResource::collection($p->audio);
        return ['package' => $p, 'audio'=>$audio];
    });


