<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AiController;

Route::get('/', [AiController::class, 'index']);
Route::post('/ask', [AiController::class, 'ask'])->name('ask');
