<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NevjegyController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\AdminMiddleware;

Route::get('/', [NevjegyController::class, 'welcome'])->name('welcome');
Route::middleware(['auth', 'admin'])->get('/admin/nevjegyek', [NevjegyController::class, 'index'])->name('admin.nevjegyek.index');



Route::get('/nevjegyek', [NevjegyController::class, 'index'])->name('nevjegyek.index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', AdminMiddleware::class])->prefix('admin/nevjegyek')->name('admin.nevjegyek.')->group(function () {
    Route::get('/', [NevjegyController::class, 'index'])->name('index');
    Route::get('/create', [NevjegyController::class, 'create'])->name('create');
    Route::post('/', [NevjegyController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [NevjegyController::class, 'edit'])->name('edit');
    Route::put('/{id}', [NevjegyController::class, 'update'])->name('update');
    Route::delete('/{id}', [NevjegyController::class, 'destroy'])->name('destroy');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/endpoint', [NevjegyController::class, 'jsonEndpoint']);

require __DIR__.'/auth.php';
