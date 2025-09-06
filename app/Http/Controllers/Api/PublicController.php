<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductPhotoResource;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductPhoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Get all active brands for public consumption.
     */
    public function brands(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');
        
        $query = Brand::query();
        
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }
        
        $brands = $query->withCount(['products' => function ($q) {
                            $q->where('active', true);
                        }])
                       ->having('products_count', '>', 0)
                       ->orderBy('name')
                       ->paginate($perPage);
        
        return response()->json([
            'data' => BrandResource::collection($brands->items()),
            'meta' => [
                'current_page' => $brands->currentPage(),
                'last_page' => $brands->lastPage(),
                'per_page' => $brands->perPage(),
                'total' => $brands->total(),
            ]
        ]);
    }

    /**
     * Get a specific brand with its active products.
     */
    public function brand(Brand $brand): JsonResponse
    {
        $brand->load(['products' => function ($query) {
            $query->where('active', true)->with('photos');
        }]);
        
        return response()->json([
            'data' => new BrandResource($brand)
        ]);
    }

    /**
     * Get all active products for public consumption.
     */
    public function products(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');
        $brandId = $request->get('brand_id');
        
        $query = Product::active()
                       ->with(['brand', 'photos']);
        
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }
        
        if ($brandId) {
            $query->where('brand_id', $brandId);
        }
        
        $products = $query->withCount('photos')
                         ->orderBy('name')
                         ->paginate($perPage);
        
        return response()->json([
            'data' => ProductResource::collection($products->items()),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    /**
     * Get a specific active product.
     */
    public function product(Product $product): JsonResponse
    {
        if (!$product->active) {
            return response()->json([
                'message' => 'Producto no disponible.'
            ], 404);
        }
        
        $product->load(['brand', 'photos']);
        
        return response()->json([
            'data' => new ProductResource($product)
        ]);
    }

    /**
     * Get products by brand for public consumption.
     */
    public function productsByBrand(Brand $brand, Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');
        
        $query = $brand->products()
                      ->active()
                      ->with(['brand', 'photos']);
        
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }
        
        $products = $query->withCount('photos')
                         ->orderBy('name')
                         ->paginate($perPage);
        
        return response()->json([
            'data' => ProductResource::collection($products->items()),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    /**
     * Get photos for a specific active product.
     */
    public function productPhotos(Product $product): JsonResponse
    {
        if (!$product->active) {
            return response()->json([
                'message' => 'Producto no disponible.'
            ], 404);
        }

        $photos = $product->photos()
                         ->ordered()
                         ->get()
                         ->map(function ($photo) {
                             return [
                                 'id' => $photo->id,
                                 'path' => $photo->path,
                                 'image_url' => $photo->image_url,
                                 'description' => $photo->description,
                                 'is_primary' => $photo->is_primary,
                                 'position' => $photo->position,
                             ];
                         });

        return response()->json([
            'success' => true,
            'data' => $photos
        ]);
    }

    /**
     * Get the primary photo for a specific active product.
     */
    public function productPrimaryPhoto(Product $product): JsonResponse
    {
        if (!$product->active) {
            return response()->json([
                'message' => 'Producto no disponible.'
            ], 404);
        }

        $primaryPhoto = $product->photos()
                              ->primary()
                              ->first();

        if (!$primaryPhoto) {
            return response()->json([
                'message' => 'No se encontró foto principal para este producto.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $primaryPhoto->id,
                'path' => $primaryPhoto->path,
                'image_url' => $primaryPhoto->image_url,
                'description' => $primaryPhoto->description,
                'is_primary' => $primaryPhoto->is_primary,
                'position' => $primaryPhoto->position,
            ]
        ]);
    }

    /**
     * Get a specific product photo (only if the product is active).
     */
    public function productPhoto(ProductPhoto $productPhoto): JsonResponse
    {
        $productPhoto->load('product');

        if (!$productPhoto->product || !$productPhoto->product->active) {
            return response()->json([
                'message' => 'Foto no disponible.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ProductPhotoResource($productPhoto)
        ]);
    }
}
