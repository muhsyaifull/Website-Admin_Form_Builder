<?php

use App\Http\Controllers\FormController;
use Illuminate\Support\Facades\Route;

// Form CRUD
Route::get('/forms', [FormController::class, 'index']);
Route::post('/forms', [FormController::class, 'store']);
Route::get('/forms/{id}', [FormController::class, 'show']);
Route::put('/forms/{id}', [FormController::class, 'update']);
Route::delete('/forms/{id}', [FormController::class, 'destroy']);

// Form Responses
Route::post('/forms/{id}/submit', [FormController::class, 'submit']);
Route::get('/forms/{id}/responses', [FormController::class, 'responses']);
