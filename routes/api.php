<?php

use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductPhotoController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\FileUploadController;
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
Route::prefix('admin')->group(function () {
    // Brand management
    Route::apiResource('brands', BrandController::class);
    
    // Product management
    Route::apiResource('products', ProductController::class);
    
    // Product photo management
    Route::apiResource('product-photos', ProductPhotoController::class);
    
    // File upload routes
    Route::post('brands/{brand}/upload-image', [FileUploadController::class, 'uploadBrandImage']);
    Route::delete('brands/{brand}/delete-image', [FileUploadController::class, 'deleteBrandImage']);
    Route::post('products/{product}/upload-photo', [FileUploadController::class, 'uploadProductPhoto']);
    Route::delete('product-photos/{productPhoto}/delete-photo', [FileUploadController::class, 'deleteProductPhoto']);
});
