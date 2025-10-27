<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicPageController;
 
Route::get('/', function () {
    return view('welcome');
});


Route::get('/pages/{slug}', [PublicPageController::class, 'show'])->name('public.page');
