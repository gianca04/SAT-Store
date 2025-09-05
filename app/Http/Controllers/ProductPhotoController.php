<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductPhotoController extends Controller
{
    /**
     * Store uploaded photos for a product
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'photos' => 'required|array|max:20',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB
        ]);

        $uploadedPhotos = [];

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
                'position' => $index + 1,
                'is_primary' => $index === 0 && $product->photos()->count() === 0,
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
            'photos' => $uploadedPhotos,
            'message' => 'Fotos subidas correctamente'
        ]);
    }

    /**
     * Update photo order and primary status
     */
    public function updateGallery(Request $request, Product $product)
    {
        $request->validate([
            'photos' => 'required|array',
            'photos.*.id' => 'required|exists:product_photos,id',
            'photos.*.position' => 'required|integer|min:1',
            'photos.*.is_primary' => 'required|boolean',
            'photos.*.description' => 'nullable|string|max:255',
        ]);

        $photos = collect($request->input('photos'));

        // Ensure only one photo is marked as primary
        $primaryCount = $photos->where('is_primary', true)->count();
        if ($primaryCount !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Debe haber exactamente una foto principal'
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
    }

    /**
     * Delete a photo
     */
    public function destroy(Request $request, Product $product, ProductPhoto $photo)
    {
        if ($photo->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Foto no encontrada'
            ], 404);
        }

        // Delete file from storage
        if (Storage::disk('public')->exists($photo->path)) {
            Storage::disk('public')->delete($photo->path);
        }

        // If this was the primary photo, promote the next one
        $wasPrimary = $photo->is_primary;
        $photo->delete();

        if ($wasPrimary) {
            $nextPrimary = $product->photos()->orderBy('position')->first();
            if ($nextPrimary) {
                $nextPrimary->update(['is_primary' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Foto eliminada correctamente'
        ]);
    }

    /**
     * Set a photo as primary
     */
    public function setPrimary(Request $request, Product $product, ProductPhoto $photo)
    {
        if ($photo->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Foto no encontrada'
            ], 404);
        }

        // Unset all other primary photos for this product
        $product->photos()->update(['is_primary' => false]);
        
        // Set this photo as primary
        $photo->update(['is_primary' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Foto principal actualizada'
        ]);
    }

    /**
     * Get all photos for a product
     */
    public function index(Product $product)
    {
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
            'photos' => $photos
        ]);
    }
}
