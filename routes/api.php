<?php

use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\V1\StreamController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('search', [SearchController::class, 'index'])->name('search');

    Route::apiResource('books', BookController::class)->only(['index', 'show']);

    Route::get('books/{book}/stream', [StreamController::class, 'stream'])->name('books.stream');
    Route::get('books/{book}/download', [StreamController::class, 'download'])->name('books.download');
    Route::get('books/{book}/sections/{section}/stream', [StreamController::class, 'streamSection'])->name('books.sections.stream');

    Route::apiResource('categories', CategoryController::class)->only(['index']);
});
