<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');
        
        $query = Brand::query();
        
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }
        
        $brands = $query->withCount('products')
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
     * Store a newly created resource in storage.
     */
    public function store(StoreBrandRequest $request): JsonResponse
    {
        $brand = Brand::create($request->validated());
        
        return response()->json([
            'message' => 'Marca creada exitosamente.',
            'data' => new BrandResource($brand)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand): JsonResponse
    {
        $brand->load(['products.photos']);
        
        return response()->json([
            'data' => new BrandResource($brand)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrandRequest $request, Brand $brand): JsonResponse
    {
        $brand->update($request->validated());
        
        return response()->json([
            'message' => 'Marca actualizada exitosamente.',
            'data' => new BrandResource($brand)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand): JsonResponse
    {
        if ($brand->products()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar la marca porque tiene productos asociados.'
            ], 422);
        }
        
        $brand->delete();
        
        return response()->json([
            'message' => 'Marca eliminada exitosamente.'
        ]);
    }
}
