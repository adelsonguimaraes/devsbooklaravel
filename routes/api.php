<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\http\Controllers as Controllers;
use App\http\Controllers\AuthController;

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

Route::get('/ping', function () {
    return ['pong'=>true];
});


// rota para não autorizado (token inválido)
Route::get('/401', [Controllers\AuthController::class, 'unauthorized'])->name('login');

Route::post('/auth/login', [Controllers\AuthController::class, 'login']);
Route::post('/auth/logout', [Controllers\AuthController::class, 'logout']);
Route::post('/auth/refresh', [Controllers\AuthController::class, 'refresh']);

// rota para criar o usuário
Route::post('/user', [Controllers\AuthController::class, 'create']);

Route::put('/user', [Controllers\UserController::class, 'update']);
Route::post('/user/avatar', [Controllers\UserController::class, 'updateAvatar']);
// Route::post('/user/cover', [Controllers\UserController::class, 'updateCover']);

// Route::get('/feed', [Controllers\FeedController::class]);
// Route::get('/user/feed', [Controllers\FeedController::class, 'userFeed']);
// Route::get('/user/{id}/feed', [Controllers\FeedController::class, 'userFeed']);

// Route::get('/user', [Controllers\UserController::class, 'read']);
// Route::get('/user/{id}', [Controllers\UserController::class, 'read']);

// Route::post('/feed', [Controllers\FeedController::class]);

// Route::post('/post/{id}/like', [Controllers\PostController::class, 'like']);
// Route::post('/post/{id}/comment', [Controllers\PostController::class, 'comment']);

// Route::get('/search', [Controllers\SearchController::class, 'search']);