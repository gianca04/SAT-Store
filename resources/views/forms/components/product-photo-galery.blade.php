<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <link rel="stylesheet" href="{{ asset('css/product-photo-gallery.css') }}">
    
    <div x-data="productPhotoGallery()" x-init="init($wire)" class="product-photo-gallery">
        <!-- File Upload Area -->
        <div class="upload-area" @drop.prevent="handleDrop($event)" @dragover.prevent @dragenter.prevent>
            <input type="file" multiple accept="image/*" @change="handleFileSelect($event)" class="file-input" x-ref="fileInput">
            <div class="upload-zone" @click="$refs.fileInput.click()">
                <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <p class="upload-text">Click para seleccionar imágenes o arrastra aquí</p>
                <p class="upload-subtext">PNG, JPG, GIF hasta 10MB</p>
            </div>
        </div>

        <!-- Photo Gallery -->
        <div class="gallery-container" x-show="photos.length > 0">
            <template x-for="(photo, index) in photos" :key="photo.id || index">
                <div class="photo-item" 
                     :class="{ 'is-primary': photo.is_primary }"
                     draggable="true"
                     @dragstart="dragStart($event, index)"
                     @dragover.prevent
                     @drop.prevent="drop($event, index)">
                    
                    <!-- Image Container -->
                    <div class="image-container">
                        <img :src="photo.preview || photo.image_url" :alt="photo.description || 'Product photo'" class="gallery-image">
                        
                        <!-- Primary Badge -->
                        <div class="primary-badge" x-show="photo.is_primary">
                            <svg class="primary-icon" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            Principal
                        </div>

                        <!-- Overlay Controls -->
                        <div class="overlay-controls">
                            <!-- Set as Primary Button -->
                            <button type="button" 
                                    @click="setPrimary(index)"
                                    :class="{ 'active': photo.is_primary }"
                                    class="control-btn primary-btn"
                                    title="Marcar como principal">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>

                            <!-- Delete Button -->
                            <button type="button" 
                                    @click="removePhoto(index)"
                                    class="control-btn delete-btn"
                                    title="Eliminar foto">
                                <svg viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Drag Handle -->
                        <div class="drag-handle">
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Photo Info -->
                    <div class="photo-info">
                        <input type="text" 
                               x-model="photo.description" 
                               @change="updatePhoto(index)"
                               placeholder="Descripción de la imagen"
                               class="description-input">
                        <div class="position-indicator">Posición: <span x-text="index + 1"></span></div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div class="empty-state" x-show="photos.length === 0">
            <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21,15 16,10 5,21"/>
            </svg>
            <p class="empty-text">No hay imágenes en la galería</p>
        </div>
    </div>

    <script src="{{ asset('js/product-photo-gallery.js') }}"></script>
</x-dynamic-component>
