<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\V1\StreamController;
use App\Http\Controllers\Api\V1\TranscriptionController;
use App\Http\Controllers\Api\V1\UploadController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('login', [AuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('me', [AuthController::class, 'me'])->name('me');

        Route::get('search', [SearchController::class, 'index'])->name('search');

        Route::post('upload', [UploadController::class, 'store'])
            ->middleware('throttle:upload')
            ->name('upload');

        Route::apiResource('books', BookController::class);

        Route::post('books/{book}/transcribe', [TranscriptionController::class, 'transcribe'])->name('books.transcribe');
        Route::get('books/{book}/transcript', [TranscriptionController::class, 'show'])->name('books.transcript');
        Route::get('books/{book}/stream', [StreamController::class, 'stream'])->name('books.stream');
        Route::get('books/{book}/download', [StreamController::class, 'download'])->name('books.download');
    });
});
