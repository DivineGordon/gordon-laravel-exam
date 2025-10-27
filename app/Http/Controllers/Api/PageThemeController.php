<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PageTheme;

class PageThemeController extends Controller
{
    public function index()
    {
        $themes = PageTheme::all();
        return response()->json($themes);
    }
}