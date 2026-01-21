<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\CouponController;

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

// Authentication routes (no auth required)
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [UserController::class, 'register']);

// Public catalog routes (no auth required)
Route::prefix('catalog')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/featured', [ProductController::class, 'featured']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
});

// Public review routes (no auth required for viewing)
Route::get('/reviews', [ReviewController::class, 'index']);
Route::get('/reviews/average', [ReviewController::class, 'averageRating']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth management
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // User profile management
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    
    // User addresses
    Route::get('/user/addresses', [UserController::class, 'addresses']);
    Route::post('/user/addresses', [UserController::class, 'storeAddress']);
    
    // User reminders
    Route::get('/user/reminders', [UserController::class, 'reminders']);
    Route::post('/user/reminders', [UserController::class, 'storeReminder']);
    
    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    
    // Reviews (creation requires auth)
    Route::post('/reviews', [ReviewController::class, 'store']);
    
    // Coupons
    Route::post('/coupons/validate', [CouponController::class, 'validate']);
});
