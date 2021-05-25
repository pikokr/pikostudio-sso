<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/auth/discord', 'App\Http\Controllers\SocialiteController@discord');
Route::get('/auth/discord/callback', 'App\Http\Controllers\SocialiteController@discordCallback');
Route::get('/auth/error', 'App\Http\Controllers\SocialiteController@error')->name('auth.error');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    Route::get('/teams/{team}/clients', 'App\Http\Controllers\Team\OAuthClients@main')->name('teams.clients');
});
