<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\VerificationController;

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
Route::post('/v1/user/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('/v1/user/verification', [VerificationController::class, 'verifyToken']);
Route::post('/v1/user/reset-password', [ResetPasswordController::class, 'resetPassword']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('v1/admin/login', [UserController::class, 'authenticate']);

// Create todo
Route::group(['middleware' => ['jwt.verify']], function() {
    Route::post('/v1/todo/add', [TaskController::class, 'create']);
    Route::get('v1/admin/users', [CustomerController::class, 'getListOfCustomers']);
    Route::get('/v1/todo/list', [TaskController::class, 'view']);
});
