<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ThreadController::class, 'index'])->name('threads.index');

Route::get('/threads', [ThreadController::class, 'index']);
Route::get('/thread/{thread}', [ThreadController::class, 'show'])->name('threads.show');

Route::middleware('auth')->group(function () {
    Route::post('/threads', [ThreadController::class, 'storeThread'])->name('threads.store');
    Route::delete('/threads/{thread}', [ThreadController::class, 'destroyThread'])->name('threads.destroy');

    Route::post('/thread/{thread}/topic', [ThreadController::class, 'storeTopic'])->name('topics.store');
    Route::patch('/thread/{thread}/topic/{topic}', [ThreadController::class, 'updateTopic'])->name('topics.update');
    Route::delete('/thread/{thread}/topic/{topic}', [ThreadController::class, 'destroyTopic'])->name('topics.destroy');
    Route::post('/topic/{topic}/reply', [ThreadController::class, 'storeReply'])->name('replies.store');
    Route::patch('/topic/{topic}/reply/{reply}', [ThreadController::class, 'updateReply'])->name('replies.update');
    Route::delete('/topic/{topic}/reply/{reply}', [ThreadController::class, 'destroyReply'])->name('replies.destroy');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
