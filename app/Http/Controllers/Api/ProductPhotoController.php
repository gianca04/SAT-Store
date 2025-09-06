<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductPhotoRequest;
use App\Http\Requests\UpdateProductPhotoRequest;
use App\Http\Requests\UploadProductPhotoRequest;
use App\Http\Requests\UpdateGalleryRequest;
use App\Http\Resources\ProductPhotoResource;
use App\Models\Product;
use App\Models\ProductPhoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductPhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Product $product = null): JsonResponse
    {
        try {
            // If product is provided via route parameter, get photos for that specific product
            if ($product) {
                $photos = $product->photos()->ordered()->get()->map(function ($photo) {
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

            // Generic listing for admin
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las fotos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Product $product = null): JsonResponse
    {
        try {
            // If product is provided via route, use gallery upload logic
            if ($product) {
                // Validate for gallery upload
                $uploadRequest = new UploadProductPhotoRequest();
                $uploadRequest->replace($request->all());
                $uploadRequest->setContainer(app());
                $validated = $uploadRequest->validated();
                
                return $this->storeGalleryPhotos($request, $product);
            }

            // Generic store for admin - validate with StoreProductPhotoRequest
            $storeRequest = new StoreProductPhotoRequest();
            $storeRequest->replace($request->all());
            $storeRequest->setContainer(app());
            $validated = $storeRequest->validated();
            
            $photo = ProductPhoto::create($validated);
            $photo->load('product');
            
            return response()->json([
                'success' => true,
                'message' => 'Foto del producto creada exitosamente.',
                'data' => new ProductPhotoResource($photo)
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la foto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store uploaded photos for a product gallery
     */
    private function storeGalleryPhotos(Request $request, Product $product): JsonResponse
    {
        try {
            $uploadedPhotos = [];
            $currentPhotoCount = $product->photos()->count();

            foreach ($request->file('photos') as $index => $file) {
                // Generate unique filename
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                
                // Store file in storage/app/public/products
                $path = $file->storeAs('products', $fileName, 'public');
                
                // Create photo record
                $photo = ProductPhoto::create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'description' => $request->input("descriptions.{$index}", ''),
                    'position' => $currentPhotoCount + $index + 1,
                    'is_primary' => $currentPhotoCount === 0 && $index === 0,
                ]);

                $uploadedPhotos[] = [
                    'id' => $photo->id,
                    'path' => $photo->path,
                    'image_url' => $photo->image_url,
                    'description' => $photo->description,
                    'is_primary' => $photo->is_primary,
                    'position' => $photo->position,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => count($uploadedPhotos) === 1 
                    ? 'Foto subida correctamente' 
                    : count($uploadedPhotos) . ' fotos subidas correctamente',
                'data' => $uploadedPhotos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir las fotos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update photo order and primary status
     */
    public function updateGallery(UpdateGalleryRequest $request, Product $product): JsonResponse
    {
        try {
            $photos = collect($request->validated()['photos']);

            // Verify all photos belong to this product
            $photoIds = $photos->pluck('id');
            $validPhotoIds = $product->photos()->whereIn('id', $photoIds)->pluck('id');
            
            if ($photoIds->count() !== $validPhotoIds->count()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Algunas fotos no pertenecen a este producto'
                ], 422);
            }

            // Update each photo
            foreach ($photos as $photoData) {
                ProductPhoto::where('id', $photoData['id'])
                    ->where('product_id', $product->id)
                    ->update([
                        'position' => $photoData['position'],
                        'is_primary' => $photoData['is_primary'],
                        'description' => $photoData['description'] ?? '',
                    ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Galería actualizada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la galería',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set a photo as primary
     */
    public function setPrimary(Request $request, Product $product, ProductPhoto $productPhoto): JsonResponse
    {
        try {
            if ($productPhoto->product_id !== $product->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Foto no encontrada'
                ], 404);
            }

            // Unset all other primary photos for this product
            $product->photos()->update(['is_primary' => false]);
            
            // Set this photo as primary
            $productPhoto->update(['is_primary' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Foto principal actualizada',
                'data' => [
                    'id' => $productPhoto->id,
                    'path' => $productPhoto->path,
                    'image_url' => $productPhoto->image_url,
                    'description' => $productPhoto->description,
                    'is_primary' => true,
                    'position' => $productPhoto->position,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al establecer foto principal',
                'error' => $e->getMessage()
            ], 500);
        }
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
    public function destroy(Product $product, ProductPhoto $productPhoto): JsonResponse
    {
        try {
            // Verify the photo belongs to the product
            if ($productPhoto->product_id !== $product->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Foto no encontrada'
                ], 404);
            }

            // Delete file from storage
            if (Storage::disk('public')->exists($productPhoto->path)) {
                Storage::disk('public')->delete($productPhoto->path);
            }

            // If this was the primary photo, promote the next one
            $wasPrimary = $productPhoto->is_primary;
            $deletedPhotoId = $productPhoto->id;
            $productPhoto->delete();

            $newPrimary = null;
            if ($wasPrimary) {
                $nextPrimary = $product->photos()->orderBy('position')->first();
                if ($nextPrimary) {
                    $nextPrimary->update(['is_primary' => true]);
                    $newPrimary = [
                        'id' => $nextPrimary->id,
                        'path' => $nextPrimary->path,
                        'image_url' => $nextPrimary->image_url,
                        'description' => $nextPrimary->description,
                        'is_primary' => true,
                        'position' => $nextPrimary->position,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Foto eliminada correctamente',
                'data' => [
                    'deleted_photo_id' => $deletedPhotoId,
                    'new_primary' => $newPrimary
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la foto',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
