<?php

use App\Http\Controllers\{
    CategoryController,
    CompanyController
};
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


Route::resource('categories', CategoryController::class);
Route::resource('companies', CompanyController::class);


Route::get('/', function () {
    return response()->json([
        'message' => 'success',
        'application' => env("APP_NAME"),
    ]);
});
