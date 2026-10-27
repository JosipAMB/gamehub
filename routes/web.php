<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ThreadController;

Route::get('/', [ThreadController::class, 'index'])
    ->name('threads.index');