<?php
use App\Http\Controllers\FormController;

Route::post('/forms', [FormController::class, 'store']);
Route::get('/forms/{id}', [FormController::class, 'show']);
Route::post('/forms/{id}/submit', [FormController::class, 'submit']);
