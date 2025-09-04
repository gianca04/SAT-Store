<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductPhotoRequest;
use App\Http\Requests\UpdateProductPhotoRequest;
use App\Http\Resources\ProductPhotoResource;
use App\Models\ProductPhoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductPhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $productId = $request->get('product_id');
        
        $query = ProductPhoto::with('product');
        
        if ($productId) {
            $query->where('product_id', $productId);
        }
        
        $photos = $query->orderBy('created_at', 'desc')
                       ->paginate($perPage);
        
        return response()->json([
            'data' => ProductPhotoResource::collection($photos->items()),
            'meta' => [
                'current_page' => $photos->currentPage(),
                'last_page' => $photos->lastPage(),
                'per_page' => $photos->perPage(),
                'total' => $photos->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductPhotoRequest $request): JsonResponse
    {
        $photo = ProductPhoto::create($request->validated());
        $photo->load('product');
        
        return response()->json([
            'message' => 'Foto del producto creada exitosamente.',
            'data' => new ProductPhotoResource($photo)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductPhoto $productPhoto): JsonResponse
    {
        $productPhoto->load('product');
        
        return response()->json([
            'data' => new ProductPhotoResource($productPhoto)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductPhotoRequest $request, ProductPhoto $productPhoto): JsonResponse
    {
        $productPhoto->update($request->validated());
        $productPhoto->load('product');
        
        return response()->json([
            'message' => 'Foto del producto actualizada exitosamente.',
            'data' => new ProductPhotoResource($productPhoto)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductPhoto $productPhoto): JsonResponse
    {
        $productPhoto->delete();
        
        return response()->json([
            'message' => 'Foto del producto eliminada exitosamente.'
        ]);
    }
}
