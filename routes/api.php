<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SimilarController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvfilmer and all of them will
| be assigned to the "api" mfilmdleware group. Make something great!
|
*/

Route::mfilmdleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/favorite', [FavoriteController::class, 'index']);

Route::prefix('/user')->group(function () {
    Route::get('/', [UserController::class, 'show']);
    Route::patch('/', [UserController::class, 'update']);
});

Route::prefix('/films')->group(function () {
    Route::post('/{film}/favorite/', [FavoriteController::class, 'store']);
    Route::delete('/{film}/favorite/', [FavoriteController::class, 'destroy']);
    Route::get('/', [FilmController::class, 'index']);
    Route::get('/{film}', [FilmController::class, 'show']);
    Route::post('/', [FilmController::class, 'store']);
    Route::patch('/{film}', [FilmController::class, 'update']);
    Route::get('/{film}/similar', [SimilarController::class, 'index']);
    Route::get('/{film}/comments', [CommentController::class, 'index']);
    Route::post('/{film}/comments', [CommentController::class, 'store']);
});

Route::prefix('/genres')->group(function () {
    Route::get('/', [GenreController::class, 'index']);
    Route::patch('/{genre}', [GenreController::class, 'update']);
});

Route::prefix('/comments')->group(function () {
    Route::patch('/{comment}', [CommentController::class, 'update']);
    Route::delete('/{comment}', [CommentController::class, 'destroy']);
});

Route::prefix('/promo')->group(function () {
    Route::get('/', [PromoController::class, 'index']);
    Route::post('/{film}', [PromoController::class, 'store']);
});
