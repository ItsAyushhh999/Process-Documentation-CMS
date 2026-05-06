<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\HealthCheckController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Webhooks\GithubPrWebhooksController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::group([], function () {
route::group(['middleware' => 'apiToken'], function () {

    Route::get('users', [UserController::class, 'allUsers']);
    Route::get('users/{userId}', [UserController::class, 'getUser']);
    Route::put('users/{userId}/edit', [UserController::class, 'editUser']);

    Route::get('projects', [ProjectController::class, 'projectList']);
    // Route::post('projects/create', [ProjectController::class, 'createProject']);

    Route::get('/{projectId}/categories', [CategoryController::class, 'categoryList']);

    Route::get('/{projectId}/tasks', [TaskController::class, 'taskList']);
    Route::get('/tasks/{task}/detail', [TaskController::class, 'taskDetails']);

    Route::put('/tasks/{task}/update', [TaskController::class, 'updateTask']);
    Route::post('/tasks/{task}/comment', [TaskController::class, 'taskComment']);
});

Route::middleware('github.webhooks.verify')->group(function () {
    Route::post('/github/webhooks/pr', [GithubPrWebhooksController::class, 'index']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/project/{projectId}/documents', [DocumentController::class, 'documentList']);
    Route::get('/document/{documentId}', [DocumentController::class, 'documentDetails']);
    Route::post('/tasks/create', [TaskController::class, 'createTask']);
    Route::get('/resource', [TaskController::class, 'resource']);
});
Route::get('/check-health', [HealthCheckController::class, 'check']);
