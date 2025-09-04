<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductPhoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    /**
     * Upload brand image.
     */
    public function uploadBrandImage(Request $request, Brand $brand): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Eliminar imagen anterior si existe
            if ($brand->foto_path && Storage::disk('public')->exists($brand->foto_path)) {
                Storage::disk('public')->delete($brand->foto_path);
            }

            // Subir nueva imagen
            $image = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('brands', $filename, 'public');

            // Actualizar el modelo
            $brand->update(['foto_path' => $path]);

            return response()->json([
                'message' => 'Imagen de marca subida exitosamente.',
                'data' => [
                    'path' => $path,
                    'url' => $brand->image_url,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al subir la imagen.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload product photo.
     */
    public function uploadProductPhoto(Request $request, Product $product): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Subir imagen
            $image = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('products', $filename, 'public');

            // Crear registro en product_photos
            $productPhoto = ProductPhoto::create([
                'product_id' => $product->id,
                'path' => $path,
                'description' => $request->input('description'),
            ]);

            return response()->json([
                'message' => 'Foto de producto subida exitosamente.',
                'data' => [
                    'id' => $productPhoto->id,
                    'path' => $path,
                    'url' => $productPhoto->image_url,
                    'description' => $productPhoto->description,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al subir la imagen.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete product photo.
     */
    public function deleteProductPhoto(ProductPhoto $productPhoto): JsonResponse
    {
        try {
            // Eliminar archivo del storage
            if ($productPhoto->path && Storage::disk('public')->exists($productPhoto->path)) {
                Storage::disk('public')->delete($productPhoto->path);
            }

            // Eliminar registro de la base de datos
            $productPhoto->delete();

            return response()->json([
                'message' => 'Foto de producto eliminada exitosamente.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la foto.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete brand image.
     */
    public function deleteBrandImage(Brand $brand): JsonResponse
    {
        try {
            // Eliminar archivo del storage
            if ($brand->foto_path && Storage::disk('public')->exists($brand->foto_path)) {
                Storage::disk('public')->delete($brand->foto_path);
            }

            // Actualizar el modelo
            $brand->update(['foto_path' => null]);

            return response()->json([
                'message' => 'Imagen de marca eliminada exitosamente.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la imagen.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
