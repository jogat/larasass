<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationContextController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

//Route::get('dashboard', [LocationContextController::class, 'test'])
//    ->middleware(['auth', 'verified', 'location'])
//    ->name('dashboard');
//
//Route::middleware(['auth'])->group(function () {
//    Route::get('/locations/select', [LocationContextController::class, 'select'])
//        ->name('locations.select');
//
//    Route::post('/locations/select', [LocationContextController::class, 'store'])
//        ->name('locations.store');
//
//    Route::post('/locations/switch', [LocationContextController::class, 'switch'])
//        ->name('locations.switch');
//});

Route::middleware(['auth'])->group(function () {
    Route::get('/locations/select', [LocationContextController::class, 'select'])->name('locations.select');
    Route::post('/locations/select', [LocationContextController::class, 'store'])->name('locations.store');
    Route::post('/locations/switch', [LocationContextController::class, 'switch'])->name('locations.switch');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});


require __DIR__.'/settings.php';


