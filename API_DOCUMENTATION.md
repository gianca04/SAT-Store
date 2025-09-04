# API de Catálogo de Productos

API RESTful para gestionar un catálogo de productos con marcas y fotos, desarrollado con Laravel siguiendo las mejores prácticas.

## Características

- **CRUD completo** para Marcas, Productos y Fotos de Productos
- **API Pública** para consumo externo (solo contenido activo)
- **API de Administración** para gestión completa
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

**Parámetros de consulta adicionales para admin:**
- `active` - Filtrar por estado activo/inactivo (productos)
- `product_id` - Filtrar por producto (fotos)

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
    "description": "Descripción de la marca",
    "foto_path": "brands/nueva-marca.jpg"
}
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

### Respuesta de error
```json
{
    "message": "Error de validación.",
    "errors": {
        "name": ["El nombre es obligatorio."]
    }
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
- `description`: opcional, máximo 500 caracteres

## Instalación y Configuración

1. Clonar el repositorio
2. Instalar dependencias: `composer install`
3. Configurar base de datos en `.env`
4. Ejecutar migraciones con datos de prueba: `php artisan migrate:fresh --seed`
5. Iniciar servidor: `php artisan serve`

## Datos de Prueba

El sistema incluye seeders con datos de marcas conocidas (Apple, Samsung, Sony, LG, Microsoft) y productos de ejemplo con sus respectivas fotos.

## Tecnologías Utilizadas

- **Laravel 11** - Framework PHP
- **Eloquent ORM** - Para manejo de base de datos
- **Form Requests** - Para validación
- **API Resources** - Para formateo de respuestas
- **Route Model Binding** - Para resolución automática de modelos
