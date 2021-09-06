<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TaskController;
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

// Customer authentication
Route::post('/v1/user/register', [CustomerController::class, 'register']);
Route::post('/v1/user/login', [CustomerController::class, 'login']);

Route::get('/user', [CustomerController::class, 'getAuthenticatedUser'])->middleware('jwt.verify');
Route::post('/v1/todo/add', [TaskController::class, 'create'])->middleware('jwt.verify');
// Auth::user()->id