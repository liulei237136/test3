<?php

use App\Http\Controllers\MediaController;
use App\Http\Controllers\PackageAudioController;
use App\Http\Controllers\PackageController;
use App\Models\Package;
use Illuminate\Foundation\Application;
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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', function(){
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/media', [MediaController::class, 'index'])->name('media.index');
    Route::post('/media', [MediaController::class, 'store'])->name('media.store');

    Route::get('/packages/create', [PackageController::class, 'create'])->name('package.create');
    Route::post('/packages', [PackageController::class, 'store'])->name('package.store');
    Route::get('/packages/{package}/audio/edit', [PackageAudioController::class, 'edit'])->name('package.audio.edit');
    Route::post('/packages/{package}/audio', [PackageAudioController::class, 'store'])->name('package.audio.store');

});
    Route::get('/packages', [PackageController::class, 'index'])->name('package.index');

