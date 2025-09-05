# Product Photo Gallery Component

## Descripción

Este componente personalizado para Filament permite gestionar una galería de imágenes de productos con funcionalidades avanzadas como:

- **Drag & Drop**: Reordenar imágenes arrastrándolas
- **Selección de imagen principal**: Marcar una imagen como principal con un clic
- **Carga múltiple**: Subir múltiples imágenes a la vez
- **Validación**: Validación de tamaño y tipo de archivo
- **Responsive**: Diseño adaptable a diferentes tamaños de pantalla
- **Notificaciones**: Feedback visual para las acciones del usuario

## Componentes del Sistema

### 1. Componente Filament (ProductPhotoGalery.php)
```php
<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class ProductPhotoGalery extends Field
{
    protected string $view = 'forms.components.product-photo-galery';
}
```

### 2. Vista Blade (product-photo-galery.blade.php)
- Interfaz de usuario con Alpine.js
- Drag & drop para archivos
- Galería visual con controles

### 3. Estilos CSS (product-photo-gallery.css)
- Diseño moderno y responsive
- Animaciones suaves
- Estados de hover y drag
- Soporte para modo oscuro

### 4. JavaScript (product-photo-gallery.js)
- Lógica de la galería
- Gestión de drag & drop
- Integración con Alpine.js
- Sistema de notificaciones

## Uso en Resources de Filament

```php
use App\Forms\Components\ProductPhotoGalery;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            // Otros campos...
            
            Forms\Components\Section::make('Galería de Imágenes')
                ->schema([
                    ProductPhotoGalery::make('photos')
                        ->label('Fotos del Producto')
                        ->helperText('Arrastra y suelta las imágenes para reordenarlas.')
                        ->columnSpanFull(),
                ]),
        ]);
}
```

## Funcionalidades

### 1. Carga de Imágenes
- **Drag & Drop**: Arrastra archivos desde el explorador
- **Click to Upload**: Haz clic en la zona de carga
- **Validación automática**: Solo acepta imágenes (PNG, JPG, GIF, WebP)
- **Límite de tamaño**: Máximo 10MB por imagen

### 2. Gestión de Galería
- **Reordenamiento**: Arrastra las imágenes para cambiar su orden
- **Imagen principal**: Haz clic en la estrella para marcar como principal
- **Eliminación**: Botón de eliminar con confirmación
- **Descripciones**: Agrega descripciones a cada imagen

### 3. Estados Visuales
- **Badge principal**: Indica cuál es la imagen principal
- **Posición**: Muestra la posición de cada imagen
- **Controles hover**: Botones que aparecen al pasar el mouse
- **Drag handle**: Indicador visual para arrastrar

## Estructura de Datos

El componente trabaja con un array de objetos con la siguiente estructura:

```javascript
{
    id: number,              // ID único
    file: File,             // Archivo original (solo para nuevas imágenes)
    preview: string,        // URL de vista previa (data URL)
    image_url: string,      // URL de la imagen guardada
    description: string,    // Descripción de la imagen
    is_primary: boolean,    // Si es la imagen principal
    position: number,       // Posición en la galería
    path: string           // Ruta del archivo en storage
}
```

## Modelo ProductPhoto

El componente se integra con el modelo `ProductPhoto` que incluye:

### Campos
- `product_id`: ID del producto
- `path`: Ruta del archivo en storage
- `description`: Descripción de la imagen
- `is_primary`: Booleano que indica si es la imagen principal
- `position`: Posición en la galería

### Relaciones
```php
// En Product.php
public function photos()
{
    return $this->hasMany(ProductPhoto::class, 'product_id')->ordered();
}

public function primaryPhoto()
{
    return $this->hasOne(ProductPhoto::class, 'product_id')->where('is_primary', true);
}
```

### Eventos del Modelo
- **Creación**: Asigna posición automática y marca como principal si es la primera
- **Guardado**: Desmarca otras imágenes cuando se marca una como principal
- **Eliminación**: Promociona la siguiente imagen como principal si se elimina la actual

## Instalación y Configuración

### 1. Archivos Requeridos
- `app/Forms/Components/ProductPhotoGalery.php`
- `resources/views/forms/components/product-photo-galery.blade.php`
- `resources/css/product-photo-gallery.css`
- `resources/js/product-photo-gallery.js`

### 2. Copiar Assets Públicos
```bash
# CSS
cp resources/css/product-photo-gallery.css public/css/

# JavaScript
cp resources/js/product-photo-gallery.js public/js/
```

### 3. Configurar Storage
Asegurar que el enlace simbólico de storage esté configurado:
```bash
php artisan storage:link
```

## Personalización

### Estilos CSS
Puedes personalizar los colores y estilos editando el archivo CSS:
```css
/* Cambiar color principal */
.photo-item.is-primary {
    border-color: #your-color;
}

/* Personalizar zona de carga */
.upload-zone:hover {
    border-color: #your-color;
}
```

### Configuración JavaScript
Modificar opciones en `product-photo-gallery.js`:
```javascript
this.options = {
    maxFileSize: 10 * 1024 * 1024, // 10MB
    allowedTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
    maxFiles: 20,
    ...options
};
```

## Eventos y Callbacks

### Alpine.js Events
```javascript
// Cuando se carga una imagen
$wire.$dispatch('photo-uploaded', { photo: newPhoto });

// Cuando se cambia el orden
$wire.$dispatch('photos-reordered', { photos: this.photos });

// Cuando se marca como principal
$wire.$dispatch('primary-photo-changed', { photoId: photo.id });
```

### JavaScript Custom Events
```javascript
// Escuchar eventos personalizados
document.addEventListener('gallery-updated', function(event) {
    console.log('Gallery updated:', event.detail);
});
```

## Validaciones

### Frontend
- Tipo de archivo (solo imágenes)
- Tamaño máximo (10MB por defecto)
- Número máximo de archivos (20 por defecto)

### Backend (Recomendado)
```php
// En tu Form o Request
'photos' => ['array', 'max:20'],
'photos.*' => ['image', 'max:10240'], // 10MB in KB
```

## Troubleshooting

### Problemas Comunes

1. **Las imágenes no se cargan**
   - Verificar que `storage:link` esté configurado
   - Comprobar permisos de directorio storage

2. **Drag & drop no funciona**
   - Verificar que Alpine.js esté cargado
   - Comprobar errores en consola del navegador

3. **Estilos no se aplican**
   - Verificar que el CSS esté en `public/css/`
   - Limpiar caché del navegador

4. **JavaScript no funciona**
   - Verificar que el JS esté en `public/js/`
   - Comprobar errores en consola

## Mejoras Futuras

- [ ] Edición de imágenes (crop, resize)
- [ ] Compresión automática de imágenes
- [ ] Carga por chunks para archivos grandes
- [ ] Integración con CDN
- [ ] Watermarks automáticos
- [ ] Generación de thumbnails
- [ ] Soporte para videos
- [ ] Galería en modo lightbox
