# Ejemplos de Pruebas del API

## Usando cURL (desde línea de comandos)

### API Pública

#### Obtener todas las marcas
```bash
curl -X GET "http://127.0.0.1:8000/api/public/brands" \
  -H "Accept: application/json"
```

#### Obtener marca específica con productos
```bash
curl -X GET "http://127.0.0.1:8000/api/public/brands/1" \
  -H "Accept: application/json"
```

#### Obtener productos públicos con filtros
```bash
curl -X GET "http://127.0.0.1:8000/api/public/products?search=iPhone&per_page=5" \
  -H "Accept: application/json"
```

#### Obtener productos de una marca específica
```bash
curl -X GET "http://127.0.0.1:8000/api/public/brands/1/products" \
  -H "Accept: application/json"
```

### API de Administración

#### Obtener todas las marcas (admin)
```bash
curl -X GET "http://127.0.0.1:8000/api/admin/brands" \
  -H "Accept: application/json"
```

#### Crear nueva marca
```bash
curl -X POST "http://127.0.0.1:8000/api/admin/brands" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Huawei",
    "description": "Empresa multinacional china de tecnología",
    "foto_path": "brands/huawei.jpg"
  }'
```

#### Actualizar marca existente
```bash
curl -X PUT "http://127.0.0.1:8000/api/admin/brands/1" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Apple Inc.",
    "description": "Empresa multinacional estadounidense líder en tecnología",
    "foto_path": "brands/apple-updated.jpg"
  }'
```

#### Crear nuevo producto
```bash
curl -X POST "http://127.0.0.1:8000/api/admin/products" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "brand_id": 1,
    "name": "iPhone 15 Pro Max",
    "description": "El iPhone más grande y avanzado de Apple",
    "active": true
  }'
```

#### Obtener productos con filtros (admin)
```bash
curl -X GET "http://127.0.0.1:8000/api/admin/products?brand_id=1&active=true&per_page=10" \
  -H "Accept: application/json"
```

#### Crear foto de producto
```bash
curl -X POST "http://127.0.0.1:8000/api/admin/product-photos" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "description": "Imagen principal del iPhone 15 Pro"
  }'
```

#### Eliminar producto
```bash
curl -X DELETE "http://127.0.0.1:8000/api/admin/products/1" \
  -H "Accept: application/json"
```

## Usando JavaScript/Fetch API

### Obtener marcas públicas
```javascript
fetch('http://127.0.0.1:8000/api/public/brands', {
  method: 'GET',
  headers: {
    'Accept': 'application/json',
  }
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error('Error:', error));
```

### Crear nueva marca (admin)
```javascript
fetch('http://127.0.0.1:8000/api/admin/brands', {
  method: 'POST',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    name: 'Tesla',
    description: 'Empresa estadounidense de vehículos eléctricos y energía limpia',
    foto_path: 'brands/tesla.jpg'
  })
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error('Error:', error));
```

## Usando Python/Requests

### Obtener productos públicos
```python
import requests

response = requests.get(
    'http://127.0.0.1:8000/api/public/products',
    headers={'Accept': 'application/json'},
    params={'search': 'iPhone', 'per_page': 5}
)

print(response.json())
```

### Crear producto (admin)
```python
import requests

data = {
    'brand_id': 1,
    'name': 'MacBook Air M3',
    'description': 'Laptop ultradelgada con chip M3',
    'active': True
}

response = requests.post(
    'http://127.0.0.1:8000/api/admin/products',
    headers={
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    },
    json=data
)

print(response.json())
```

## Pruebas con Postman

### Configuración de Colección

1. **Base URL**: `http://127.0.0.1:8000/api`
2. **Headers comunes**:
   - `Accept: application/json`
   - `Content-Type: application/json` (para POST/PUT)

### Requests de ejemplo:

#### GET Marcas Públicas
- **URL**: `{{base_url}}/public/brands`
- **Method**: GET
- **Headers**: Accept: application/json

#### POST Nueva Marca
- **URL**: `{{base_url}}/admin/brands`
- **Method**: POST
- **Headers**: Accept: application/json, Content-Type: application/json
- **Body (JSON)**:
  ```json
  {
    "name": "Xiaomi",
    "description": "Empresa china de electrónicos",
    "foto_path": "brands/xiaomi.jpg"
  }
  ```

## Comandos de Laravel para pruebas

### Verificar datos en base de datos
```bash
php artisan tinker
```

```php
// Ver todas las marcas
\App\Models\Brand::all();

// Ver productos activos con sus marcas
\App\Models\Product::active()->with('brand')->get();

// Ver fotos de un producto específico
\App\Models\Product::find(1)->photos;

// Contar productos por marca
\App\Models\Brand::withCount('products')->get();
```

### Limpiar y recargar datos de prueba
```bash
php artisan migrate:fresh --seed
```

## Respuestas Esperadas

### Éxito - Lista de marcas
```json
{
  "data": [
    {
      "id": 1,
      "name": "Apple",
      "description": "Empresa multinacional estadounidense...",
      "foto_path": "brands/apple.jpg",
      "created_at": "2025-09-04 21:42:29",
      "updated_at": "2025-09-04 21:42:29",
      "products_count": 3
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 5
  }
}
```

### Error de validación
```json
{
  "message": "El nombre de la marca es obligatorio. (and 1 more error)",
  "errors": {
    "name": ["El nombre de la marca es obligatorio."],
    "brand_id": ["La marca seleccionada no existe."]
  }
}
```

### Éxito - Creación
```json
{
  "message": "Marca creada exitosamente.",
  "data": {
    "id": 6,
    "name": "Nueva Marca",
    "description": "Descripción de la marca",
    "foto_path": "brands/nueva-marca.jpg",
    "created_at": "2025-09-04 22:15:30",
    "updated_at": "2025-09-04 22:15:30",
    "products_count": 0
  }
}
```
