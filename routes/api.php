<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:api', 'scope:identify'])->get('/user', function (Request $request) {
    $user = $request->user();
    if (!$user->tokenCan('discord')) {
        $user = $user->makeHidden('discord_id');
    }
    return $user;
});
