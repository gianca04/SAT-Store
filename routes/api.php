<?php

use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductPhotoController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\FileUploadController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| These routes are used for authentication (login, register, logout).
| They don't require authentication except for logout and user info.
|
*/
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logout-all', [AuthController::class, 'logoutAll']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
|
| These routes are publicly accessible and don't require authentication.
| They return only active/published content for public consumption.
|
*/
Route::prefix('public')->group(function () {
    // Public brands endpoints
    Route::get('brands', [PublicController::class, 'brands']);
    Route::get('brands/{brand}', [PublicController::class, 'brand']);
    Route::get('brands/{brand}/products', [PublicController::class, 'productsByBrand']);
    
    // Public products endpoints
    Route::get('products', [PublicController::class, 'products']);
    Route::get('products/{product}', [PublicController::class, 'product']);
    
    // Public product photos endpoints
    Route::get('products/{product}/photos', [PublicController::class, 'productPhotos']);
    Route::get('products/{product}/photos/primary', [PublicController::class, 'productPrimaryPhoto']);
    Route::get('product-photos/{productPhoto}', [PublicController::class, 'productPhoto']);
});

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| These routes require authentication and are used for CRUD operations.
| They provide full access to all resources regardless of status.
|
*/
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    // Brand management
    Route::apiResource('brands', BrandController::class);
    
    // Product management
    Route::apiResource('products', ProductController::class);
    
    // Product photo management
    Route::apiResource('product-photos', ProductPhotoController::class);
    
    // Photo gallery management
    Route::prefix('products/{product}/photos')->group(function () {
        Route::get('/', [ProductPhotoController::class, 'index']);
        Route::post('/', [ProductPhotoController::class, 'store']);
        Route::put('gallery', [ProductPhotoController::class, 'updateGallery']);
        Route::put('{productPhoto}/primary', [ProductPhotoController::class, 'setPrimary']);
        Route::delete('{productPhoto}', [ProductPhotoController::class, 'destroy']);
    });
    
    // File upload routes
    Route::post('brands/{brand}/upload-image', [FileUploadController::class, 'uploadBrandImage']);
    Route::delete('brands/{brand}/delete-image', [FileUploadController::class, 'deleteBrandImage']);
    Route::post('products/{product}/upload-photo', [FileUploadController::class, 'uploadProductPhoto']);
    Route::delete('product-photos/{productPhoto}/delete-photo', [FileUploadController::class, 'deleteProductPhoto']);
});
