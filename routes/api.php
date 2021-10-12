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
    'middleware' => 'auth:api',
], function () {

    Route::group([
        'namespace' => 'App\Http\Controllers',
        'prefix' => 'team',
    ], function () {
        Route::get("/user/team", "TeamController@getUserTeam"); // Auth Protected
        Route::get("/show/{team}", "TeamController@getTeam"); // Policy Protected Route -- TeamPolicy

        Route::middleware(['auth.role:Admin,Manager'])->group(function () {
            Route::get("/all", "TeamController@getTeams");
            Route::get("/allWithMembers", "TeamController@getTeamsWithMembers");
            Route::post("/create", "TeamController@createTeam");
            Route::delete("/delete/{team}", "TeamController@deleteTeam");
            Route::put('edit/{id}', "TeamController@updateTeam");
        });
    });

    Route::group([
        'namespace' => 'App\Http\Controllers',
        'prefix' => 'user',
    ], function () {
        Route::get("/show/{user}", "UserController@show"); // Policy Protected Route -- UserPolicy@view
        Route::post('edit/{user}', "UserController@update"); // Policy Protected Route -- UserPolicy@update

        Route::middleware(['auth.role:Admin,Manager'])->group(function () {
            Route::get("/all", "UserController@getAllUsers");
            Route::get("/allWithTeam", "UserController@getUsersWithTeam");
            Route::delete("/delete/{user}", "UserController@destroy");
        });
    });

    Route::group([
        'namespace' => 'App\Http\Controllers\Task',
        'prefix' => 'task',
        'middleware' => 'auth.role:Admin,Manager,Leader',
    ], function () {
        Route::post("/create", "TaskController@store");
        Route::put("/edit/{task}", "TaskController@update");
        Route::delete("/delete/{task}", "TaskController@destroy");
    });

    Route::group([
        'namespace' => 'App\Http\Controllers\Todo',
        'prefix' => 'todo',
    ], function () {
        Route::middleware(['auth.role:Admin,Manager,Leader'])->group(function () {
            Route::post("/create", "TodoController@store");
            Route::put("/edit/{todo}", "TodoController@update");
            Route::delete("/delete/{todo}", "TodoController@destroy");
        });
    });

    Route::group([
        'namespace' => 'App\Http\Controllers\Course',
        'prefix' => 'course',
    ], function () {
        Route::middleware(['auth.role:Admin,Manager,Leader'])->group(function () {
            Route::post("/create", "CourseController@store");
            Route::put("/edit/{course}", "CourseController@update");
            Route::delete("/delete/{course}", "CourseController@destroy");

            Route::group([
                'prefix' => 'article',
            ], function () {
                Route::post("/create", "ArticleController@store");
                Route::put("/edit/{article}", "ArticleController@update");
                Route::delete("/delete/{article}", "ArticleController@destroy");
            });

            Route::group([
                'prefix' => 'video',
            ], function () {
                Route::post("/create", "VideoController@uploadVideo");
                Route::post("/createViaUrl", "VideoController@uploadVideoViaUrl");
                Route::put("/edit/{video}", "VideoController@update");
                Route::delete("/delete/{video}", "VideoController@destroy");
            });

            Route::group([
                'prefix' => 'quiz',
            ], function () {
                Route::post("/create", "QuizController@store");
                Route::put("/edit/{quiz}", "QuizController@update");
                Route::delete("/delete/{quiz}", "QuizController@destroy");

                Route::group([
                    'prefix' => 'question',
                ], function () {
                    Route::post("/create", "QuestionController@store");
                    Route::put("/edit/{question}", "QuestionController@update");
                    Route::delete("/delete/{question}", "QuestionController@destroy");
                });
            });

        });
    });

});
