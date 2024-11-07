<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Pets\PetController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Profile\ProfileController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'auth']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth.sanctum:sanctum');

Route::middleware('auth.sanctum:sanctum')->group(function () {

    //HOME
    Route::get('/home', [PetController::class, 'index']);
    Route::get('/', [PetController::class, 'index']);

    //PETS
    Route::get('/pets', [PetController::class, 'index']);
    Route::post('/pet', [PetController::class, 'store']);
    Route::get('/pet/{id}', [PetController::class, 'show']);
    Route::put('/pet/{id}', [PetController::class, 'update']);
    Route::delete('/pet/{id}', [PetController::class, 'destroy']);

    //PROFILE
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);

});