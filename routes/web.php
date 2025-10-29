<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicPageController;
use Illuminate\Support\Facades\DB;  // Add this line
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

Route::get('/pages/{slug}', [PublicPageController::class, 'show'])->name('public.page');

Route::get('/db-test', function () {
    try {

        // Log connection info to console
        Log::info('=== DATABASE CONNECTION TEST ===');
        Log::info('Status: Connected');
        Log::info('=== DATABASE CONNECTION TEST ===');
        Log::info('Route Path: ' . request()->path());
        Log::info('Full URL: ' . request()->fullUrl());
        Log::info('APP_ENV: ' . env('APP_ENV', "PRODUCTION"));

        Log::info('Database: ' . env('DB_SOCKET', env('DB_HOST')));

        // Test basic connection
        DB::connection()->getPdo();
        $dbName = DB::connection()->getDatabaseName();
        Log::info('Database: ' . $dbName);

        $results = [
            'status' => 'Connected',
            'database' => $dbName,
            'connection_name' => env('DB_SOCKET', env('DB_HOST')),

        ];

        // Check if page_themes table exists
        if (Schema::hasTable('page_themes')) {
            $results['page_themes_table'] = 'EXISTS';

            // Get count of records
            $count = DB::table('page_themes')->count();
            $results['page_themes_count'] = $count;

            // Get all themes if any exist
            if ($count > 0) {
                $themes = DB::table('page_themes')->get();
                $results['themes'] = $themes;
            } else {
                $results['themes'] = 'No themes found (table is empty)';
            }
        } else {
            $results['page_themes_table'] = 'DOES NOT EXIST (run migrations)';
        }

        return response()->json($results, 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'FAILED',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'connection_name' => env('DB_SOCKET', env('DB_HOST')),

        ], 500);
    }
});


// CATCH-ALL: This must be the last route. it is used for the vue frontend
Route::get('/{any}', function () {
    // This view is usually a simple Blade file that
    // contains the main <div id="app"> element.
    return view('app_vue_frontend');
})->where('any', '.*');
