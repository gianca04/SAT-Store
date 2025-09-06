# Ejemplos de Uso - API ProductPhoto

## Endpoints Públicos

### 1. Obtener todas las fotos de un producto
```bash
# Curl
curl -X GET "http://your-app.com/api/public/products/1/photos" \
     -H "Accept: application/json"

# JavaScript (Fetch)
fetch('/api/public/products/1/photos')
  .then(response => response.json())
  .then(data => {
    console.log('Fotos del producto:', data.data);
  });

# PHP (Guzzle)
$response = $client->get('/api/public/products/1/photos');
$photos = json_decode($response->getBody(), true);
```

**Respuesta:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "path": "products/photo1.jpg",
            "image_url": "http://your-app.com/storage/products/photo1.jpg",
            "description": "Vista frontal",
            "is_primary": true,
            "position": 1
        },
        {
            "id": 2,
            "path": "products/photo2.jpg",
            "image_url": "http://your-app.com/storage/products/photo2.jpg",
            "description": "Vista lateral",
            "is_primary": false,
            "position": 2
        }
    ]
}
```

### 2. Obtener solo la foto principal de un producto
```bash
# Curl
curl -X GET "http://your-app.com/api/public/products/1/photos/primary" \
     -H "Accept: application/json"

# JavaScript (Fetch)
fetch('/api/public/products/1/photos/primary')
  .then(response => response.json())
  .then(data => {
    console.log('Foto principal:', data.data);
    document.getElementById('main-image').src = data.data.image_url;
  });
```

**Respuesta:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "path": "products/photo1.jpg",
        "image_url": "http://your-app.com/storage/products/photo1.jpg",
        "description": "Vista frontal",
        "is_primary": true,
        "position": 1
    }
}
```

### 3. Obtener una foto específica
```bash
# Curl
curl -X GET "http://your-app.com/api/public/product-photos/1" \
     -H "Accept: application/json"
```

## Endpoints Administrativos

### 4. Subir múltiples fotos a un producto
```bash
# Curl
curl -X POST "http://your-app.com/api/admin/products/1/photos" \
     -H "Authorization: Bearer your-token" \
     -F "photos[]=@/path/to/photo1.jpg" \
     -F "photos[]=@/path/to/photo2.jpg" \
     -F "descriptions[0]=Vista frontal" \
     -F "descriptions[1]=Vista trasera"
```

```javascript
// JavaScript (FormData)
const formData = new FormData();
formData.append('photos[]', file1);
formData.append('photos[]', file2);
formData.append('descriptions[0]', 'Vista frontal');
formData.append('descriptions[1]', 'Vista trasera');

fetch('/api/admin/products/1/photos', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer ' + token
    },
    body: formData
})
.then(response => response.json())
.then(data => console.log('Fotos subidas:', data));
```

### 5. Actualizar el orden y configuración de la galería
```bash
# Curl
curl -X PUT "http://your-app.com/api/admin/products/1/photos/gallery" \
     -H "Authorization: Bearer your-token" \
     -H "Content-Type: application/json" \
     -d '{
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
         }
       ]
     }'
```

```javascript
// JavaScript
const galleryUpdate = {
    photos: [
        {
            id: 2,
            position: 1,
            is_primary: true,
            description: "Nueva foto principal"
        },
        {
            id: 1,
            position: 2,
            is_primary: false,
            description: "Segunda foto"
        }
    ]
};

fetch('/api/admin/products/1/photos/gallery', {
    method: 'PUT',
    headers: {
        'Authorization': 'Bearer ' + token,
        'Content-Type': 'application/json'
    },
    body: JSON.stringify(galleryUpdate)
})
.then(response => response.json())
.then(data => console.log('Galería actualizada:', data));
```

### 6. Marcar una foto como principal
```bash
# Curl
curl -X PUT "http://your-app.com/api/admin/products/1/photos/2/primary" \
     -H "Authorization: Bearer your-token" \
     -H "Accept: application/json"
```

### 7. Eliminar una foto
```bash
# Curl
curl -X DELETE "http://your-app.com/api/admin/products/1/photos/2" \
     -H "Authorization: Bearer your-token" \
     -H "Accept: application/json"
```

## Uso Frontend Completo

### React Component Example
```jsx
import React, { useState, useEffect } from 'react';

const ProductGallery = ({ productId }) => {
    const [photos, setPhotos] = useState([]);
    const [primaryPhoto, setPrimaryPhoto] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadPhotos();
    }, [productId]);

    const loadPhotos = async () => {
        try {
            setLoading(true);
            
            // Cargar todas las fotos
            const photosResponse = await fetch(`/api/public/products/${productId}/photos`);
            const photosData = await photosResponse.json();
            
            if (photosData.success) {
                setPhotos(photosData.data);
                
                // Buscar la foto principal
                const primary = photosData.data.find(photo => photo.is_primary);
                setPrimaryPhoto(primary);
            }
        } catch (error) {
            console.error('Error loading photos:', error);
        } finally {
            setLoading(false);
        }
    };

    if (loading) return <div>Cargando fotos...</div>;

    if (!photos.length) return <div>No hay fotos disponibles</div>;

    return (
        <div className="product-gallery">
            {/* Foto principal */}
            <div className="main-photo">
                {primaryPhoto && (
                    <img 
                        src={primaryPhoto.image_url} 
                        alt={primaryPhoto.description}
                        className="main-image"
                    />
                )}
            </div>

            {/* Thumbnails */}
            <div className="thumbnails">
                {photos.map(photo => (
                    <img
                        key={photo.id}
                        src={photo.image_url}
                        alt={photo.description}
                        className={`thumbnail ${photo.is_primary ? 'active' : ''}`}
                        onClick={() => setPrimaryPhoto(photo)}
                    />
                ))}
            </div>
        </div>
    );
};

export default ProductGallery;
```

### Vue.js Component Example
```vue
<template>
  <div class="product-gallery" v-if="!loading">
    <!-- Foto principal -->
    <div class="main-photo">
      <img 
        v-if="primaryPhoto"
        :src="primaryPhoto.image_url" 
        :alt="primaryPhoto.description"
        class="main-image"
      />
    </div>

    <!-- Thumbnails -->
    <div class="thumbnails">
      <img
        v-for="photo in photos"
        :key="photo.id"
        :src="photo.image_url"
        :alt="photo.description"
        :class="['thumbnail', { active: photo.is_primary }]"
        @click="setPrimaryPhoto(photo)"
      />
    </div>
  </div>
  
  <div v-else>Cargando fotos...</div>
</template>

<script>
export default {
  name: 'ProductGallery',
  props: {
    productId: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      photos: [],
      primaryPhoto: null,
      loading: true
    };
  },
  mounted() {
    this.loadPhotos();
  },
  methods: {
    async loadPhotos() {
      try {
        this.loading = true;
        
        const response = await fetch(`/api/public/products/${this.productId}/photos`);
        const data = await response.json();
        
        if (data.success) {
          this.photos = data.data;
          this.primaryPhoto = data.data.find(photo => photo.is_primary);
        }
      } catch (error) {
        console.error('Error loading photos:', error);
      } finally {
        this.loading = false;
      }
    },
    
    setPrimaryPhoto(photo) {
      this.primaryPhoto = photo;
    }
  }
};
</script>
```

## Manejo de Errores

### Productos Inactivos
```javascript
fetch('/api/public/products/999/photos')
  .then(response => {
    if (!response.ok) {
      throw new Error('Producto no disponible');
    }
    return response.json();
  })
  .catch(error => {
    console.error('Error:', error.message);
    // Mostrar mensaje al usuario
  });
```

### Sin Foto Principal
```javascript
fetch('/api/public/products/1/photos/primary')
  .then(response => response.json())
  .then(data => {
    if (!data.success) {
      // Usar imagen placeholder
      document.getElementById('main-image').src = '/images/no-photo.png';
    } else {
      document.getElementById('main-image').src = data.data.image_url;
    }
  });
```
