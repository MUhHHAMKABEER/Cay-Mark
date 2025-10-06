<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PackageController;

// API route for fetching packages by role
Route::get('/packages/{role}', [PackageController::class, 'byRole']);
