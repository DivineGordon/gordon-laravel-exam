<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicPageController;


Route::get('/pages/{slug}', [PublicPageController::class, 'show'])->name('public.page');



// CATCH-ALL: This must be the last route. it is used for the vue frontend
Route::get('/{any}', function () {
    // This view is usually a simple Blade file that
    // contains the main <div id="app"> element.
    return view('app_vue_frontend');
})->where('any', '.*');