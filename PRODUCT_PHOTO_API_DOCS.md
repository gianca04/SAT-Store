# API REST ProductPhoto - Documentación

Esta API REST para el modelo `ProductPhoto` sigue las mejores prácticas de Laravel y proporciona endpoints completos para la gestión de fotos de productos.

## Estructura de la API

### Rutas Disponibles

#### Public API (Sin autenticación)
```php
// Fotos de productos públicas
Route::get('public/products/{product}/photos', [PublicController::class, 'productPhotos']);
Route::get('public/products/{product}/photos/primary', [PublicController::class, 'productPrimaryPhoto']); 
Route::get('public/product-photos/{productPhoto}', [PublicController::class, 'productPhoto']);
```

#### Admin API (Protegidas)
```php
// CRUD completo para fotos de productos
Route::apiResource('admin/product-photos', ProductPhotoController::class);

// Gestión de galería por producto
Route::prefix('admin/products/{product}/photos')->group(function () {
    Route::get('/', [ProductPhotoController::class, 'index']);          // Listar fotos del producto
    Route::post('/', [ProductPhotoController::class, 'store']);         // Subir fotos (archivos)
    Route::put('gallery', [ProductPhotoController::class, 'updateGallery']); // Actualizar galería
    Route::put('{photo}/primary', [ProductPhotoController::class, 'setPrimary']); // Marcar como principal
    Route::delete('{photo}', [ProductPhotoController::class, 'destroy']); // Eliminar foto
});
```

## Endpoints Públicos

### 1. Obtener fotos de un producto (Público)
```http
GET /api/public/products/{product_id}/photos
```

**Descripción**: Obtiene todas las fotos de un producto activo, ordenadas por posición.

**Respuesta:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "path": "products/uuid1.jpg",
            "image_url": "http://example.com/storage/products/uuid1.jpg",
            "description": "Foto principal del producto",
            "is_primary": true,
            "position": 1
        },
        {
            "id": 2,
            "path": "products/uuid2.jpg",
            "image_url": "http://example.com/storage/products/uuid2.jpg",
            "description": "Vista lateral",
            "is_primary": false,
            "position": 2
        }
    ]
}
```

**Errores:**
```json
{
    "message": "Producto no disponible."
}
```

### 2. Obtener foto principal de un producto (Público)
```http
GET /api/public/products/{product_id}/photos/primary
```

**Descripción**: Obtiene únicamente la foto principal de un producto activo.

**Respuesta:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "path": "products/uuid1.jpg",
        "image_url": "http://example.com/storage/products/uuid1.jpg",
        "description": "Foto principal del producto",
        "is_primary": true,
        "position": 1
    }
}
```

**Errores:**
```json
{
    "message": "No se encontró foto principal para este producto."
}
```

### 3. Obtener foto específica (Público)
```http
GET /api/public/product-photos/{photo_id}
```

**Descripción**: Obtiene una foto específica si pertenece a un producto activo.

**Respuesta:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "product_id": 1,
        "path": "products/uuid1.jpg",
        "image_url": "http://example.com/storage/products/uuid1.jpg",
        "description": "Foto principal del producto",
        "is_primary": true,
        "position": 1,
        "created_at": "2025-09-05 10:00:00",
        "updated_at": "2025-09-05 10:00:00",
        "product": {
            // Datos del producto relacionado (solo si está activo)
        }
    }
}
```

**Errores:**
```json
{
    "message": "Foto no disponible."
}
```

## Endpoints Detallados (Admin)

### 1. Listar todas las fotos (Admin)
```http
GET /api/admin/product-photos
```

**Parámetros de consulta:**
- `per_page` (int): Número de elementos por página (default: 15)
- `product_id` (int): Filtrar por ID de producto

**Respuesta:**
```json
{
    "data": [
        {
            "id": 1,
            "product_id": 1,
            "path": "products/uuid.jpg",
            "image_url": "http://example.com/storage/products/uuid.jpg",
            "description": "Foto principal del producto",
            "is_primary": true,
            "position": 1,
            "created_at": "2025-09-05 10:00:00",
            "updated_at": "2025-09-05 10:00:00",
            "product": {
                // Datos del producto relacionado
            }
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 75
    }
}
```

### 2. Mostrar foto específica
```http
GET /api/admin/product-photos/{id}
```

**Respuesta:**
```json
{
    "data": {
        "id": 1,
        "product_id": 1,
        "path": "products/uuid.jpg",
        "image_url": "http://example.com/storage/products/uuid.jpg",
        "description": "Foto principal del producto",
        "is_primary": true,
        "position": 1,
        "created_at": "2025-09-05 10:00:00",
        "updated_at": "2025-09-05 10:00:00",
        "product": {
            // Datos del producto relacionado
        }
    }
}
```

### 3. Crear nueva foto (Admin)
```http
POST /api/admin/product-photos
Content-Type: application/json
```

**Body:**
```json
{
    "product_id": 1,
    "path": "products/new-photo.jpg",
    "description": "Nueva foto del producto",
    "is_primary": false,
    "position": 3
}
```

**Validaciones:**
- `product_id`: requerido, debe existir en la tabla products
- `path`: requerido, string, máximo 255 caracteres
- `description`: opcional, string, máximo 500 caracteres
- `is_primary`: opcional, boolean
- `position`: opcional, entero, mínimo 1

### 4. Actualizar foto
```http
PUT /api/admin/product-photos/{id}
Content-Type: application/json
```

**Body:**
```json
{
    "product_id": 1,
    "path": "products/updated-photo.jpg",
    "description": "Descripción actualizada",
    "is_primary": true,
    "position": 1
}
```

### 5. Eliminar foto
```http
DELETE /api/admin/product-photos/{id}
```

## Gestión de Galería de Productos

### 6. Listar fotos de un producto específico
```http
GET /api/admin/products/{product_id}/photos
```

**Respuesta:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "path": "products/uuid1.jpg",
            "image_url": "http://example.com/storage/products/uuid1.jpg",
            "description": "Foto principal",
            "is_primary": true,
            "position": 1
        },
        {
            "id": 2,
            "path": "products/uuid2.jpg", 
            "image_url": "http://example.com/storage/products/uuid2.jpg",
            "description": "Vista lateral",
            "is_primary": false,
            "position": 2
        }
    ]
}
```

### 7. Subir fotos a la galería (Archivos)
```http
POST /api/admin/products/{product_id}/photos
Content-Type: multipart/form-data
```

**Body (Form Data):**
```
photos[]: [archivo1.jpg]
photos[]: [archivo2.jpg]
descriptions[0]: "Descripción de la primera foto"
descriptions[1]: "Descripción de la segunda foto"
```

**Validaciones:**
- `photos`: requerido, array, máximo 20 archivos
- `photos.*`: archivo requerido, imagen, tipos: jpeg,png,jpg,gif,webp, máximo 10MB
- `descriptions`: opcional, array
- `descriptions.*`: opcional, string, máximo 255 caracteres

**Respuesta:**
```json
{
    "success": true,
    "message": "2 fotos subidas correctamente",
    "data": [
        {
            "id": 3,
            "path": "products/uuid3.jpg",
            "image_url": "http://example.com/storage/products/uuid3.jpg",
            "description": "Descripción de la primera foto",
            "is_primary": false,
            "position": 3
        },
        {
            "id": 4,
            "path": "products/uuid4.jpg",
            "image_url": "http://example.com/storage/products/uuid4.jpg", 
            "description": "Descripción de la segunda foto",
            "is_primary": false,
            "position": 4
        }
    ]
}
```

### 8. Actualizar galería (orden, descripciones, foto principal)
```http
PUT /api/admin/products/{product_id}/photos/gallery
Content-Type: application/json
```

**Body:**
```json
{
    "photos": [
        {
            "id": 2,
            "position": 1,
            "is_primary": true,
            "description": "Nueva foto principal"
        },
        {
            "id": 1,
            "position": 2,
            "is_primary": false,
            "description": "Segunda foto"
        },
        {
            "id": 3,
            "position": 3,
            "is_primary": false,
            "description": "Tercera foto"
        }
    ]
}
```

**Validaciones:**
- Debe haber exactamente una foto marcada como principal
- No puede haber posiciones duplicadas
- Todas las fotos deben pertenecer al producto especificado

### 9. Marcar foto como principal
```http
PUT /api/admin/products/{product_id}/photos/{photo_id}/primary
```

**Respuesta:**
```json
{
    "success": true,
    "message": "Foto principal actualizada",
    "data": {
        "id": 2,
        "path": "products/uuid2.jpg",
        "image_url": "http://example.com/storage/products/uuid2.jpg",
        "description": "Nueva foto principal",
        "is_primary": true,
        "position": 1
    }
}
```

### 10. Eliminar foto de la galería
```http
DELETE /api/admin/products/{product_id}/photos/{photo_id}
```

**Respuesta:**
```json
{
    "success": true,
    "message": "Foto eliminada correctamente",
    "data": {
        "deleted_photo_id": 2,
        "new_primary": {
            "id": 1,
            "path": "products/uuid1.jpg",
            "image_url": "http://example.com/storage/products/uuid1.jpg",
            "description": "Nueva foto principal automática",
            "is_primary": true,
            "position": 1
        }
    }
}
```

## Características Especiales

### Gestión Automática de Foto Principal
- Cuando se crea la primera foto de un producto, se marca automáticamente como principal
- Cuando se marca una foto como principal, las otras se desmarcan automáticamente
- Cuando se elimina la foto principal, la siguiente en orden se promociona automáticamente

### Gestión de Posiciones
- Las posiciones se asignan automáticamente al crear fotos
- Se pueden reordenar mediante el endpoint de actualización de galería

### Validaciones Avanzadas
- El sistema usa Form Requests específicos para cada operación
- Validaciones personalizadas para asegurar consistencia de datos
- Mensajes de error personalizados en español

### Manejo de Archivos
- Los archivos se almacenan en `storage/app/public/products`
- Se generan nombres únicos usando UUID
- Los archivos se eliminan del almacenamiento al borrar la foto

## Códigos de Respuesta HTTP

- `200 OK`: Operación exitosa
- `201 Created`: Recurso creado exitosamente
- `422 Unprocessable Entity`: Errores de validación
- `404 Not Found`: Recurso no encontrado
- `500 Internal Server Error`: Error del servidor

## Estructura de Errores

```json
{
    "success": false,
    "message": "Descripción del error",
    "errors": {
        "campo": ["mensaje de error específico"]
    }
}
```
