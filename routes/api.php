<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthDoctorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Manage Apis
Route::post('/login-email', [AuthController::class, 'loginByEmail']);

Route::middleware('auth:sanctum')->group(function(){

    Route::post('/logout' , [AuthController::class , 'logout']);
    Route::post('/admin/create-account', [AuthController::class, 'createAccountByAdmin'])->middleware('permission:manage-accounts');
});

// Doctor Apis
Route::post('/doctor/login-email' , [AuthDoctorController::class , 'loginDoctorByEmail']);
Route::post('/doctor/register', [AuthDoctorController::class, 'registerDoctor']);

Route::middleware('auth:sanctum')->prefix('doctor')->group(function(){
    
    Route::post('/logout' , [AuthDoctorController::class , 'logout']);
});

// Reset Password Apis

Route::prefix('reset')->group(function () {
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/verify-code', [AuthController::class, 'verifyResetCode']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});
