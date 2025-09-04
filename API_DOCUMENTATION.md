# API de Catálogo de Productos

API RESTful para gestionar un catálogo de productos con marcas y fotos, desarrollado con Laravel siguiendo las mejores prácticas.

## Características

- **CRUD completo** para Marcas, Productos y Fotos de Productos
- **API Pública** para consumo externo (solo contenido activo)
- **API de Administración** para gestión completa
- **Subida de archivos** para imágenes de marcas y productos
- **Validación robusta** con Form Requests personalizados
- **Recursos API** para formateo consistente de respuestas
- **Relaciones Eloquent** optimizadas
- **Paginación** en todos los listados
- **Filtrado y búsqueda** por texto
- **Datos de prueba** incluidos via seeders

## Estructura de Base de Datos

### Brands (Marcas)
- `id` - ID único
- `name` - Nombre de la marca (único)
- `description` - Descripción (opcional)
- `foto_path` - Ruta de la foto (opcional)
- `created_at`, `updated_at` - Timestamps

### Products (Productos)
- `id` - ID único
- `brand_id` - FK a brands
- `name` - Nombre del producto
- `description` - Descripción (opcional)
- `active` - Estado activo/inactivo
- `created_at`, `updated_at` - Timestamps

### Product Photos (Fotos de Productos)
- `id` - ID único
- `product_id` - FK a products
- `path` - Ruta del archivo de imagen
- `description` - Descripción de la foto (opcional)
- `created_at`, `updated_at` - Timestamps

## Endpoints de la API

### API Pública (`/api/public/`)

Endpoints para consumo público que solo retornan contenido activo.

#### Marcas
- `GET /api/public/brands` - Listar marcas con productos activos
- `GET /api/public/brands/{id}` - Ver marca específica con sus productos activos
- `GET /api/public/brands/{id}/products` - Productos activos de una marca

#### Productos
- `GET /api/public/products` - Listar productos activos
- `GET /api/public/products/{id}` - Ver producto activo específico

**Parámetros de consulta disponibles:**
- `per_page` - Elementos por página (default: 15)
- `search` - Búsqueda por nombre o descripción
- `brand_id` - Filtrar por marca (solo en productos)

### API de Administración (`/api/admin/`)

Endpoints para gestión completa del catálogo (requiere autenticación).

#### Marcas
- `GET /api/admin/brands` - Listar todas las marcas
- `POST /api/admin/brands` - Crear nueva marca
- `GET /api/admin/brands/{id}` - Ver marca específica
- `PUT /api/admin/brands/{id}` - Actualizar marca
- `DELETE /api/admin/brands/{id}` - Eliminar marca

#### Productos
- `GET /api/admin/products` - Listar todos los productos
- `POST /api/admin/products` - Crear nuevo producto
- `GET /api/admin/products/{id}` - Ver producto específico
- `PUT /api/admin/products/{id}` - Actualizar producto
- `DELETE /api/admin/products/{id}` - Eliminar producto

#### Fotos de Productos
- `GET /api/admin/product-photos` - Listar todas las fotos
- `POST /api/admin/product-photos` - Crear nueva foto
- `GET /api/admin/product-photos/{id}` - Ver foto específica
- `PUT /api/admin/product-photos/{id}` - Actualizar foto
- `DELETE /api/admin/product-photos/{id}` - Eliminar foto

#### Subida de Archivos
- `POST /api/admin/brands/{id}/upload-image` - Subir imagen de marca
- `DELETE /api/admin/brands/{id}/delete-image` - Eliminar imagen de marca
- `POST /api/admin/products/{id}/upload-photo` - Subir foto de producto
- `DELETE /api/admin/product-photos/{id}/delete-photo` - Eliminar foto de producto

**Parámetros de consulta adicionales para admin:**
- `active` - Filtrar por estado activo/inactivo (productos)
- `product_id` - Filtrar por producto (fotos)

## Subida de Archivos

### Configuración
- **Tamaño máximo:** 2MB por archivo
- **Formatos soportados:** JPEG, PNG, JPG, GIF, WebP
- **Almacenamiento:** Local en `storage/app/public/`
- **Acceso público:** A través de `/storage/`

### Subir imagen de marca
```bash
POST /api/admin/brands/{id}/upload-image
Content-Type: multipart/form-data

# Formulario
image: [archivo de imagen]
```

**Respuesta exitosa:**
```json
{
    "message": "Imagen de marca subida exitosamente.",
    "data": {
        "path": "brands/1704422400_abcd123456.jpg",
        "url": "http://localhost:8000/storage/brands/1704422400_abcd123456.jpg"
    }
}
```

### Subir foto de producto
```bash
POST /api/admin/products/{id}/upload-photo
Content-Type: multipart/form-data

# Formulario
image: [archivo de imagen]
description: [descripción opcional]
```

**Respuesta exitosa:**
```json
{
    "message": "Foto de producto subida exitosamente.",
    "data": {
        "id": 1,
        "path": "products/1704422400_xyz789.jpg",
        "url": "http://localhost:8000/storage/products/1704422400_xyz789.jpg",
        "description": "Vista frontal del producto"
    }
}
```

### Eliminar archivos
```bash
DELETE /api/admin/brands/{id}/delete-image
DELETE /api/admin/product-photos/{id}/delete-photo
```

## Ejemplos de Uso

### Obtener productos públicos con filtros
```bash
GET /api/public/products?search=iPhone&brand_id=1&per_page=10
```

### Crear una nueva marca (admin)
```bash
POST /api/admin/brands
Content-Type: application/json

{
    "name": "Nueva Marca",
    "description": "Descripción de la marca"
}
```

### Subir imagen a marca existente
```bash
POST /api/admin/brands/1/upload-image
Content-Type: multipart/form-data

# Archivo en campo 'image'
```

### Crear un nuevo producto (admin)
```bash
POST /api/admin/products
Content-Type: application/json

{
    "brand_id": 1,
    "name": "Nuevo Producto",
    "description": "Descripción del producto",
    "active": true
}
```

### Subir foto a producto existente
```bash
POST /api/admin/products/1/upload-photo
Content-Type: multipart/form-data

# Archivo en campo 'image'
# description: "Foto principal del producto"
```

## Formato de Respuestas

### Respuesta exitosa con datos
```json
{
    "data": [...],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 75
    }
}
```

### Respuesta de creación/actualización
```json
{
    "message": "Recurso creado exitosamente.",
    "data": {...}
}
```

### Respuesta de error de validación
```json
{
    "message": "Error de validación.",
    "errors": {
        "name": ["El nombre es obligatorio."],
        "image": ["El archivo debe ser una imagen válida."]
    }
}
```

### Respuesta de error de subida
```json
{
    "message": "Error al subir la imagen.",
    "error": "El archivo excede el tamaño máximo permitido."
}
```

## Validaciones

### Marcas
- `name`: requerido, único, máximo 255 caracteres
- `description`: opcional, máximo 1000 caracteres
- `foto_path`: opcional, máximo 500 caracteres

### Productos
- `brand_id`: requerido, debe existir en la tabla brands
- `name`: requerido, máximo 255 caracteres
- `description`: opcional, máximo 1000 caracteres
- `active`: booleano (opcional, default: true)

### Fotos de Productos
- `product_id`: requerido, debe existir en la tabla products
- `path`: generado automáticamente en subida
- `description`: opcional, máximo 500 caracteres

### Archivos de Imagen
- `image`: requerido para subida
- **Tipos MIME permitidos:** image/jpeg, image/png, image/jpg, image/gif, image/webp
- **Tamaño máximo:** 2048 KB (2MB)
- **Nombres de archivo:** Se generan automáticamente con timestamp + string aleatorio

## Respuestas de API Resources

### BrandResource
```json
{
    "id": 1,
    "name": "Apple",
    "description": "Empresa multinacional...",
    "foto_path": "brands/apple_logo.jpg",
    "image_url": "http://localhost:8000/storage/brands/apple_logo.jpg",
    "created_at": "2025-01-01 12:00:00",
    "updated_at": "2025-01-01 12:00:00",
    "products_count": 5
}
```

### ProductResource
```json
{
    "id": 1,
    "brand_id": 1,
    "name": "iPhone 15 Pro",
    "description": "El smartphone más avanzado...",
    "active": true,
    "created_at": "2025-01-01 12:00:00",
    "updated_at": "2025-01-01 12:00:00",
    "photos_count": 3,
    "brand": { /* BrandResource */ }
}
```

### ProductPhotoResource
```json
{
    "id": 1,
    "product_id": 1,
    "path": "products/iphone_front.jpg",
    "image_url": "http://localhost:8000/storage/products/iphone_front.jpg",
    "description": "Vista frontal del iPhone",
    "created_at": "2025-01-01 12:00:00",
    "updated_at": "2025-01-01 12:00:00"
}
```

## Frontend Vue 3 + TypeScript

### Características del Frontend
- **Vue 3** con Composition API
- **TypeScript** para tipado seguro
- **Pinia** para gestión de estado
- **Vue Router** para navegación
- **Tailwind CSS** para estilos
- **Componente FileUpload** para subida de archivos
- **Validación de archivos** en tiempo real
- **Preview de imágenes** antes de subir
- **Drag & Drop** para archivos

### Páginas Disponibles
- **Home** (`/`) - Página principal con productos destacados
- **Marcas** (`/brands`) - Listado de marcas con filtros
- **Productos** (`/products`) - Catálogo de productos con búsqueda
- **Detalle de Marca** (`/brands/{id}`) - Productos de una marca específica
- **Detalle de Producto** (`/products/{id}`) - Información completa del producto
- **Admin** (`/admin`) - Panel de administración completo

### Funcionalidades de Subida en Frontend
- **Drag & Drop** de archivos sobre zona de subida
- **Preview inmediato** de imágenes seleccionadas
- **Validación de tipo y tamaño** antes de enviar
- **Indicadores de progreso** durante la subida
- **Manejo de errores** con mensajes claros
- **Integración con stores** para actualización automática

## Instalación y Configuración

1. Clonar el repositorio
2. Instalar dependencias PHP: `composer install`
3. Instalar dependencias Node.js: `npm install`
4. Configurar base de datos en `.env`
5. Ejecutar migraciones con datos de prueba: `php artisan migrate:fresh --seed`
6. Crear enlace simbólico de storage: `php artisan storage:link`
7. Compilar assets: `npm run build`
8. Iniciar servidor: `php artisan serve`

## Estructura de Archivos

### Backend (Laravel)
```
app/
├── Http/
│   ├── Controllers/Api/
│   │   ├── BrandController.php
│   │   ├── ProductController.php
│   │   ├── ProductPhotoController.php
│   │   ├── PublicController.php
│   │   └── FileUploadController.php
│   ├── Requests/
│   └── Resources/
├── Models/
│   ├── Brand.php
│   ├── Product.php
│   └── ProductPhoto.php
```

### Frontend (Vue 3 + TypeScript)
```
resources/js/
├── components/
│   └── FileUpload.vue
├── pages/
│   ├── Home.vue
│   ├── Brands.vue
│   ├── Products.vue
│   ├── BrandDetail.vue
│   ├── ProductDetail.vue
│   └── Admin.vue
├── stores/
│   ├── brands.ts
│   ├── products.ts
│   └── admin.ts
├── services/
│   └── api.ts
└── types/
    └── index.ts
```

### Storage
```
storage/app/public/
├── brands/        # Imágenes de marcas
└── products/      # Fotos de productos
```

## Datos de Prueba

El sistema incluye seeders con datos de marcas conocidas (Apple, Samsung, Sony, LG, Microsoft) y productos de ejemplo con sus respectivas fotos.

## Tecnologías Utilizadas

### Backend
- **Laravel 11** - Framework PHP
- **Eloquent ORM** - Para manejo de base de datos
- **Form Requests** - Para validación
- **API Resources** - Para formateo de respuestas
- **Storage Facade** - Para manejo de archivos

### Frontend
- **Vue 3** - Framework JavaScript con Composition API
- **TypeScript** - Tipado estático
- **Pinia** - Gestión de estado reactivo
- **Vue Router** - Enrutamiento SPA
- **Tailwind CSS** - Framework de estilos utilitarios
- **Vite** - Bundler y herramientas de desarrollo
- **Axios** - Cliente HTTP para API calls
