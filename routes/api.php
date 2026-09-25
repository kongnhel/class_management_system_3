<?php

use App\Http\Controllers\UtilityController;
use Illuminate\Support\Facades\Route;

Route::get('/user', [UtilityController::class, 'apiUser'])->middleware('auth:sanctum');
