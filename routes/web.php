<?php

use App\Http\Controllers\CountyController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Counties routes
Route::get("/counties", [CountyController::class, "index"])->name('counties.index');
Route::get("/counties/create", [CountyController::class, "create"])->name('counties.create');
Route::post('/counties', [CountyController::class, 'store'])->name('counties.store');
Route::get("/counties/{id}", [CountyController::class, "show"])->name('counties.show');
Route::get("/counties/{id}/edit", [CountyController::class, "edit"])->name('counties.edit');
Route::put("/counties/{id}", [CountyController::class, "update"])->name('counties.update');
Route::delete("/counties/{id}", [CountyController::class, "destroy"])->name('counties.destroy');
Route::get('/counties/export/csv', [CountyController::class, 'exportCsv'])->name('counties.export.csv');
Route::get('/counties/export/pdf', [CountyController::class, 'exportPdf'])->name('counties.export.pdf');

// Places routes
Route::get("/places", [PlaceController::class, "index"])->name('places.index');
Route::get("/places/create", [PlaceController::class, "create"])->name('places.create');
Route::post('/places', [PlaceController::class, 'store'])->name('places.store');
Route::get("/places/{id}", [PlaceController::class, "show"])->name('places.show');
Route::get("/places/{id}/edit", [PlaceController::class, "edit"])->name('places.edit');
Route::put("/places/{id}", [PlaceController::class, "update"])->name('places.update');
Route::delete("/places/{id}", [PlaceController::class, "destroy"])->name('places.destroy');
Route::get('/places/export/csv', [PlaceController::class, 'exportCsv'])->name('places.export.csv');
Route::get('/places/export/pdf', [PlaceController::class, 'exportPdf'])->name('places.export.pdf');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
