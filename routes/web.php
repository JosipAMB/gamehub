<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ThreadController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ThreadController::class, 'index'])->name('threads.index');

Route::get('/threads', [ThreadController::class, 'index']);
Route::get('/thread/{thread}', [ThreadController::class, 'show'])->name('threads.show');

Route::post('/thread/{thread}/topic', [ThreadController::class, 'storeTopic'])
    ->middleware('auth')
    ->name('topics.store');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';