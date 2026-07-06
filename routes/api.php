<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Doctor\CategoryController;
use App\Http\Controllers\Admin\ManageDoctorController;
use App\Http\Controllers\Admin\ManageOrderController;
use App\Http\Controllers\Admin\ManageProductController;
use App\Http\Controllers\Admin\ManageUserController;
use App\Http\Controllers\Doctor\AuthDoctorController;
use App\Http\Controllers\Doctor\DoctorProductController;
use App\Http\Controllers\Doctor\OrderController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Manage Apis
Route::post('/login-email', [AuthController::class, 'loginByEmail']);
Route::post('/login-phone', [AuthController::class, 'loginByPhone']);

Route::middleware('auth:sanctum')->group(function(){

    Route::post('/logout' , [AuthController::class , 'logout']);
    Route::post('/admin/create-account', [AuthController::class, 'createAccountByAdmin'])->middleware('permission:manage-accounts');

    // Manage Doctor Apis
    Route::post('/employee/doctor/{doctor}/status', [ManageDoctorController::class, 'updateDoctorAccountStatus'])->middleware('permission:manage-accounts');

    // Manage products Apis
    Route::post('/admin/products/create-product', [ManageProductController::class, 'createProduct'])->middleware('permission:add-product');

    // Manage Orders Apis
    Route::post('/admin/orders/{id}/status', [ManageOrderController::class, 'updateOrderStatus'])->middleware('permission:accept-order');
    Route::get('employee/get-order-details/{id}' , [ManageOrderController::class , 'getOrderDetails']);
    Route::get('employee/doctor/{id}/orders' , [ManageOrderController::class , 'getDoctorOrders']);

    // Manage Users Apis
    Route::get('/admin/show-users' , [ManageUserController::class , 'getUsersWithFilter'])->middleware('permission:manage-accounts');

});

// Doctor Apis
Route::post('/doctor/login-email' , [AuthDoctorController::class , 'loginDoctorByEmail']);
Route::post('/doctor/login-phone' , [AuthDoctorController::class , 'loginDoctorByPhone']);
Route::post('/doctor/register', [AuthDoctorController::class, 'registerDoctor']);

Route::middleware('auth:sanctum')->prefix('doctor')->group(function(){
    // Doctor Orders Apis
    Route::post('/create-order', [OrderController::class, 'CreateNewOrder'])->middleware('permission:create-order');
    Route::get('/get-order-details/{id}' , [OrderController::class , 'getOrderDetails']);
    Route::get('/orders' , [OrderController::class , 'getDoctorOrders']);
});

Route::get('/products', [DoctorProductController::class, 'getProducts']);
Route::get('/products/{slug}', [DoctorProductController::class, 'showProduct']);
Route::get('/categories' , [CategoryController::class , 'getCategories']);
Route::get('/category/{slug}' , [CategoryController::class , 'getProductsInCategory']);
// Reset Password Apis

Route::prefix('reset')->group(function () {
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/verify-code', [AuthController::class, 'verifyResetCode']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});
