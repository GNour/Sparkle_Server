<?php

use App\Http\Controllers\AuthController;
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

Route::group([
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'auth',
], function () {
    Route::post('/login', [AuthController::class, "login"]);
    Route::post('/register', [AuthController::class, "register"]);

    Route::middleware(['auth:api'])->group(function () {
        Route::post('/logout', [AuthController::class, "logout"]);
        Route::post('/refresh', [AuthController::class, "refresh"]);
    });
});

Route::group([
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'team',
], function () {

    Route::get("/all", "TeamController@getTeams");
    Route::get("/allWithMembers", "TeamController@getTeamsWithMembers");
    Route::get("/userTeam", "TeamController@getUserTeam");
    Route::post("/create", "TeamController@createTeam");
    Route::post("/delete", "TeamController@deleteTeam");
    Route::put('edit/{id}', "TeamController@updateTeam");
});
