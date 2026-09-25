<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\StudentController;

Route::apiResource('students', StudentController::class);

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
	Route::get('me', [AuthController::class, 'me']);
	Route::post('logout', [AuthController::class, 'logout']);
	Route::apiResource('projects', ProjectController::class);
	Route::apiResource('projects.tasks', TaskController::class)->shallow();
});