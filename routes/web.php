<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LinkedinController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

/** Google OAuth routes */
Route::get('auth/google/redirect', [GoogleController::class, 'handleGoogleRedirect']);
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

/** Linkedin OAuth routes */
Route::get('auth/linkedin/redirect', [LinkedinController::class, 'handleLinkedinRedirect']);
Route::get('auth/linkedin/callback', [LinkedinController::class, 'handleLinkedinCallback']);
