<?php

use Illuminate\Support\Facades\Route;

// Serves the audio-library frontend (React/Vite SPA) for every route that
// isn't matched by the API or the Filament admin panel, so client-side
// routes like /books work on a full page load too.
Route::fallback(function () {
    return response()->file(resource_path('spa/index.html'));
});
