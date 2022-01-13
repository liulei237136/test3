<?php

use App\Http\Controllers\AudioController;
use App\Http\Controllers\CommitController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\SearchController;
use App\Http\Resources\AudioResource;
use App\Models\Package;
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
    if (auth()->user()) {
        return Redirect::route('dashboard');

    }
    return Inertia::render('Index');
});
Route::get('/test', function () {
    return view('test');
});

Route::get('/packages/{package}', [PackageController::class, 'show'])->name('package.show');
Route::get('/packages/{package}/audio', [PackageController::class, 'audio'])->name('package.audio');
Route::get('/packages/{package}/pulls', [PackageController::class, 'pulls'])->name('package.pulls');
Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::get('/commits/{commit}/audio', [CommitController::class, 'audio'])->name('commit.audio');

// Route::get('/package{package}/pulls',[PullController::class,'index']);

// Route::get('/package/{package}/compare', [CompareController::class, 'compare'])->name('compare.compare');

Route::get('/packages/{toPackage}/compare/packages/{fromPackage}', [CompareController::class, 'package'])->name('compare.package');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/media', [MediaController::class, 'index'])->name('media.index');
    Route::post('/media', [MediaController::class, 'store'])->name('media.store');

    Route::post('/packages/{package}/clone', [PackageController::class, 'clone'])->name('package.clone');
    Route::get('/create_package', [PackageController::class, 'create'])->name('package.create');
    Route::get('/packages/{package}/init', [PackageController::class, 'init'])->name('package.init');
    Route::post('/packages', [PackageController::class, 'store'])->name('package.store');
    Route::patch('/packages/{package}', [PackageController::class, 'update'])->name('package.update');

    Route::post('/packages/{package}/toggle_favorite', [PackageController::class, 'toggleFavorite'])->name('package.toggle_favorite');

    Route::post('/package/{package}/commits', [CommitController::class, 'store'])->name('package.commit.store');

    Route::post('/commits', [CommitController::class, 'store'])->name('commit.store');

    Route::post('/audio', [AudioController::class, 'store'])->name('audio.store');

});

Route::get('/test/{package}', function ($package) {
    $p = Package::without('audio')->findOrFail($package);
    $audio = AudioResource::collection($p->audio);
    return ['package' => $p, 'audio' => $audio];
});
