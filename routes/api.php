<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\TestController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/* a. GET method to return the request content */
Route::get('/a', [TestController::class, 'a']);

/* b. POST method to return the request content */
Route::post('/b', [TestController::class, 'b']);

/* c. GET method, which throws an expected error, such as a request field format error */
Route::get('/c', [TestController::class, 'c']);

/* d. GET method, which throws an unexpected error */
Route::get('/d', [TestController::class, 'd']);

/* e. GET method, which use the url query 's' for the logical test below */
Route::get('/e', [TestController::class, 'e']);


