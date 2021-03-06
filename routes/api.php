<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Each route have different protection...
|
 */

Route::group([
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'auth',
], function () {
    Route::post('/login', [AuthController::class, "login"]);
    Route::post('/register', [AuthController::class, "register"]);
    Route::get('/me', [AuthController::class, "checkUser"]);

    Route::middleware(['auth:api'])->group(function () {
        Route::post('/logout', [AuthController::class, "logout"]);
        Route::post('/refresh', [AuthController::class, "refresh"]);
    });
});

Route::group([
    'middleware' => 'auth:api',
], function () {

    // Realtime chat app for presentation
    Route::group([
        'prefix' => 'message',
        'namespace' => 'App\Http\Controllers',
    ], function () {
        Route::post("/send", "ChatController@sendMessage");
        Route::get("/messages", "ChatController@getMessages");
        Route::put("/read/{user}", "ChatController@readMessages");
    });

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

        // NOTE ROUTES, ADDED AFTER V1.0
        Route::group([
            'prefix' => 'note',
            'middleware' => 'auth.role:Admin,Manager,Leader',
        ], function () {
            Route::post("add", "NoteController@store");
            Route::delete("delete/{note}", "NoteController@destroy");
        });
    });

    Route::group([
        'namespace' => 'App\Http\Controllers\Task',
        'prefix' => 'task',
        'middleware' => 'auth',
    ], function () {

        Route::get("/show/{task}", "TaskController@show");
        Route::get("/assigned", "TaskController@getAssignedTasks"); // Controller Protected
        Route::put("/complete/{task}", "TaskController@completeTask");
        Route::get("/finished", "TaskController@getFinishedTasks");

        Route::middleware(['auth.role:Admin,Manager,Leader'])->group(function () {
            Route::post("/create", "TaskController@store");
            Route::put("/edit/{task}", "TaskController@update"); // Policy Protected Route -- TaskPolicy@deleteOrUpdate
            Route::delete("/delete/{task}", "TaskController@destroy"); // Policy Protected Route -- TaskPolicy@deleteOrUpdate
            Route::put("/assign/{task}", "TaskController@assignTask");
            Route::put("/unassign/{task}", "TaskController@unassignTask");
            Route::get("/unassigned", "TaskController@getUnassignedTasks");
            Route::put("/finish/{task}", "TaskController@finishTask");
            Route::put("/uncomplete/{task}/{user}", "TaskController@uncompleteTask");
        });

        Route::get("/all", "TaskController@getAllTasks");
    });

    // TODO RELATED ROUTES
    Route::group([
        'namespace' => 'App\Http\Controllers\Todo',
        'prefix' => 'todo',
        'middleware' => 'auth',
    ], function () {

        Route::get("show/{todo}", "TodoController@show");

        Route::middleware(['auth.role:Admin,Manager,Leader'])->group(function () {
            Route::post("/create", "TodoController@store");
            Route::put("/edit/{todo}", "TodoController@update"); // Policy Protected Route -- TodoPolicy@deleteOrUpdate
            Route::delete("/delete/{todo}", "TodoController@destroy"); // Policy Protected Route -- TodoPolicy@deleteOrUpdate
        });
    });

    // COURSE RELATED ROUTES
    Route::group([
        'namespace' => 'App\Http\Controllers\Course',
        'prefix' => 'course',
        'middleware' => 'auth',
    ], function () {

        Route::get("show/{course}", "CourseController@show");
        Route::put("take/{course}", "CourseController@takeCourse");
        Route::put("complete/{course}", "CourseController@completeCourse");

        Route::prefix('video')->group(function () {
            Route::put('watch/{video}', "VideoController@watchVideo");
            Route::put('leftat/{video}', "VideoController@editVideoLeftAt");
            Route::put('complete/{video}', "VideoController@completeVideo");
        });

        Route::prefix('article')->group(function () {
            Route::put('read/{article}', "ArticleController@readArticle");
            Route::put('complete/{article}', "ArticleController@completeArticle");
        });

        Route::prefix('quiz')->group(function () {
            Route::put('take/{quiz}', "QuizController@takeQuiz");
            Route::put('complete/{quiz}', "QuizController@completeQuiz");
        });

        Route::middleware(['auth.role:Admin,Manager,Leader'])->group(function () {
            Route::post("/create", "CourseController@store");
            Route::put("/edit/{course}", "CourseController@update"); // Policy Protected Route -- CoursePolicy@deleteOrUpdate
            Route::delete("/delete/{course}", "CourseController@destroy"); // Policy Protected Route -- CoursePolicy@deleteOrUpdate

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
                Route::post("/createviaurl", "VideoController@uploadVideoViaUrl");
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
                    Route::put("/create/{quiz}", "QuestionController@store");
                    Route::put("/edit/{question}", "QuestionController@update");
                    Route::delete("/delete/{question}", "QuestionController@destroy");

                    Route::group([
                        'prefix' => 'answer',
                    ], function () {
                        Route::put("/create/{question}", "AnswerController@store");
                        Route::delete("/delete/{answer}", "AnswerController@destroy");
                    });
                });
            });

        });
    });

});

// SERVER SIDE RENDERING ROUTES --- KEY REQUIRED ---

Route::group([
    'prefix' => 'server',
], function () {
    Route::post("/courses", [CourseController::class, "getCoursesForTask"]);
    Route::post("/users", [UserController::class, "getUsersBasicInfo"]);
    Route::post("/teams", [TeamController::class, "getTeamsBasicInfo"]);
    Route::post("/managers", [UserController::class, "getManagers"]);
    Route::post("/dashboard", [UserController::class, "getGeneralStatsForManagers"]);
});

// ARDUINO BOARD FIGERPRINT SCANNER --- KEY REQUIRED ---

Route::group([
    'prefix' => 'iot',
], function () {
    Route::post("/attendance", [AttendanceController::class, "getAllAttendance"]);
    Route::put("/attend/{uid}", [AttendanceController::class, "attendIn"]);
    Route::put("/leave/{uid}", [AttendanceController::class, "leave"]);
});
