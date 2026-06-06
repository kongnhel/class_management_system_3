<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/debug', function () {
    return response()->json([
        'php_version' => PHP_VERSION,
        'app_env' => env('APP_ENV'),
        'app_key' => env('APP_KEY') ? 'SET' : 'NOT SET',
        'db_host' => env('DB_HOST') ? 'SET' : 'NOT SET',
        'db_connection' => env('DB_CONNECTION'),
        'db_error' => null,
        'db_status' => function () {
            try {
                DB::connection()->getPdo();

                return 'Connected';
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        },
    ]);
});

Route::get('/debug-test', function () {
    $dbStatus = 'failed';
    $dbError = null;

    try {
        DB::connection()->getPdo();
        $dbStatus = 'connected';
    } catch (\Exception $e) {
        $dbError = $e->getMessage();
    }

    return response()->json([
        'app_env' => env('APP_ENV'),
        'app_key' => env('APP_KEY') ? 'SET' : 'NOT SET',
        'db_host' => env('DB_HOST'),
        'db_port' => env('DB_PORT'),
        'db_status' => $dbStatus,
        'db_error' => $dbError,
    ]);
});
