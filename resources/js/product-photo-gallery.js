/**
 * Product Photo Gallery Component
 * Handles image upload, reordering, and primary selection using API endpoints
 */

class ProductPhotoGallery {
    constructor(container, options = {}) {
        this.container = container;
        this.options = {
            maxFileSize: 10 * 1024 * 1024, // 10MB
            allowedTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            maxFiles: 20,
            productId: null,
            apiBaseUrl: '/api/admin/products',
            ...options
        };
        
        this.photos = [];
        this.draggedIndex = null;
        this.wire = null;
        this.uploading = false;
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadExistingPhotos();
    }

    setupEventListeners() {
        // File input change
        const fileInput = this.container.querySelector('.file-input');
        if (fileInput) {
            fileInput.addEventListener('change', (e) => this.handleFileSelect(e));
        }

        // Drag and drop
        const uploadZone = this.container.querySelector('.upload-zone');
        if (uploadZone) {
            uploadZone.addEventListener('click', () => fileInput?.click());
            uploadZone.addEventListener('dragover', (e) => this.handleDragOver(e));
            uploadZone.addEventListener('dragleave', (e) => this.handleDragLeave(e));
            uploadZone.addEventListener('drop', (e) => this.handleDrop(e));
        }
    }

    async loadExistingPhotos() {
        if (!this.options.productId) return;

        try {
            const response = await fetch(`${this.options.apiBaseUrl}/${this.options.productId}/photos`);
            const result = await response.json();
            
            if (result.success) {
                this.photos = result.data || [];
                this.render();
                this.updateWire();
            }
        } catch (error) {
            console.error('Error loading photos:', error);
            this.showNotification('Error al cargar las fotos existentes', 'error');
        }
    }

    handleFileSelect(event) {
        const files = Array.from(event.target.files);
        this.processFiles(files);
        event.target.value = ''; // Reset input
    }

    handleDragOver(event) {
        event.preventDefault();
        event.stopPropagation();
        event.currentTarget.classList.add('dragover');
    }

    handleDragLeave(event) {
        event.preventDefault();
        event.stopPropagation();
        event.currentTarget.classList.remove('dragover');
    }

    handleDrop(event) {
        event.preventDefault();
        event.stopPropagation();
        event.currentTarget.classList.remove('dragover');
        
        const files = Array.from(event.dataTransfer.files).filter(file => 
            this.options.allowedTypes.includes(file.type)
        );
        
        this.processFiles(files);
    }

    async processFiles(files) {
        if (this.uploading) {
            this.showNotification('Ya hay una subida en progreso', 'info');
            return;
        }

        if (this.photos.length + files.length > this.options.maxFiles) {
            this.showNotification(`Máximo ${this.options.maxFiles} imágenes permitidas`, 'error');
            return;
        }

        const validFiles = files.filter(file => {
            if (file.size > this.options.maxFileSize) {
                this.showNotification(`El archivo ${file.name} es demasiado grande. Máximo 10MB.`, 'error');
                return false;
            }

            if (!this.options.allowedTypes.includes(file.type)) {
                this.showNotification(`Tipo de archivo no permitido: ${file.type}`, 'error');
                return false;
            }

            return true;
        });

        if (validFiles.length === 0) return;

        // Upload via API
        await this.uploadPhotosToAPI(validFiles);
    }

    async uploadPhotosToAPI(files) {
        if (!this.options.productId) {
            this.showNotification('ID del producto no disponible', 'error');
            return;
        }

        this.uploading = true;
        this.showUploadProgress();

        try {
            const formData = new FormData();
            files.forEach((file, index) => {
                formData.append('photos[]', file);
                formData.append(`descriptions[${index}]`, '');
            });

            const response = await fetch(`${this.options.apiBaseUrl}/${this.options.productId}/photos`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            const result = await response.json();

            if (result.success) {
                // Add new photos to the gallery
                this.photos.push(...result.data);
                this.render();
                this.updateWire();
                this.showNotification(result.message, 'success');
            } else {
                throw new Error(result.message || 'Error al subir las fotos');
            }
        } catch (error) {
            console.error('Upload error:', error);
            this.showNotification('Error al subir las fotos: ' + error.message, 'error');
        } finally {
            this.uploading = false;
            this.hideUploadProgress();
        }
    }

    async setPrimary(index) {
        const photo = this.photos[index];
        if (!photo || !this.options.productId) return;

        try {
            const response = await fetch(`${this.options.apiBaseUrl}/${this.options.productId}/photos/${photo.id}/primary`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            const result = await response.json();

            if (result.success) {
                // Update local state
                this.photos.forEach((p, i) => {
                    p.is_primary = i === index;
                });
                this.render();
                this.updateWire();
                this.showNotification(result.message, 'success');
            } else {
                throw new Error(result.message || 'Error al establecer foto principal');
            }
        } catch (error) {
            console.error('Set primary error:', error);
            this.showNotification('Error al establecer foto principal: ' + error.message, 'error');
        }
    }

    async removePhoto(index) {
        if (!confirm('¿Estás seguro de que quieres eliminar esta foto?')) {
            return;
        }

        const photo = this.photos[index];
        if (!photo || !this.options.productId) return;

        try {
            const response = await fetch(`${this.options.apiBaseUrl}/${this.options.productId}/photos/${photo.id}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            const result = await response.json();

            if (result.success) {
                // Remove from local state
                this.photos.splice(index, 1);
                
                // If there's a new primary photo, update it
                if (result.data.new_primary) {
                    const newPrimaryIndex = this.photos.findIndex(p => p.id === result.data.new_primary.id);
                    if (newPrimaryIndex !== -1) {
                        this.photos[newPrimaryIndex].is_primary = true;
                    }
                }
                
                this.updatePositions();
                this.render();
                this.updateWire();
                this.showNotification(result.message, 'info');
            } else {
                throw new Error(result.message || 'Error al eliminar la foto');
            }
        } catch (error) {
            console.error('Delete error:', error);
            this.showNotification('Error al eliminar la foto: ' + error.message, 'error');
        }
    }

    async updateDescription(index, description) {
        const photo = this.photos[index];
        if (!photo) return;

        photo.description = description;
        
        // Debounce API call
        clearTimeout(this.descriptionTimeout);
        this.descriptionTimeout = setTimeout(() => {
            this.syncGalleryState();
        }, 1000);
    }

    async syncGalleryState() {
        if (!this.options.productId || this.photos.length === 0) return;

        try {
            const response = await fetch(`${this.options.apiBaseUrl}/${this.options.productId}/photos/gallery`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    photos: this.photos.map((photo, index) => ({
                        id: photo.id,
                        position: index + 1,
                        is_primary: photo.is_primary,
                        description: photo.description || ''
                    }))
                })
            });

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message || 'Error al sincronizar la galería');
            }
        } catch (error) {
            console.error('Sync error:', error);
            this.showNotification('Error al sincronizar la galería', 'error');
        }
    }

    startDrag(event, index) {
        this.draggedIndex = index;
        event.target.closest('.photo-item').classList.add('dragging');
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/html', event.target.outerHTML);
    }

    handleDragOverPhoto(event, targetIndex) {
        event.preventDefault();
        if (this.draggedIndex !== null && this.draggedIndex !== targetIndex) {
            event.currentTarget.classList.add('drag-over');
        }
    }

    handleDragLeavePhoto(event) {
        event.currentTarget.classList.remove('drag-over');
    }

    dropPhoto(event, targetIndex) {
        event.preventDefault();
        event.currentTarget.classList.remove('drag-over');
        
        if (this.draggedIndex !== null && this.draggedIndex !== targetIndex) {
            this.reorderPhotos(this.draggedIndex, targetIndex);
        }
        
        this.resetDragState();
    }

    reorderPhotos(fromIndex, toIndex) {
        const draggedPhoto = this.photos[fromIndex];
        this.photos.splice(fromIndex, 1);
        this.photos.splice(toIndex, 0, draggedPhoto);
        
        this.updatePositions();
        this.render();
        this.updateWire();
        this.syncGalleryState();
        this.showNotification('Orden actualizado', 'success');
    }

    resetDragState() {
        this.draggedIndex = null;
        this.container.querySelectorAll('.photo-item').forEach(item => {
            item.classList.remove('dragging', 'drag-over');
        });
    }

    updatePositions() {
        this.photos.forEach((photo, index) => {
            photo.position = index + 1;
        });
    }

    showUploadProgress() {
        const uploadZone = this.container.querySelector('.upload-zone');
        if (uploadZone) {
            uploadZone.classList.add('uploading');
            uploadZone.innerHTML = `
                <div class="upload-progress">
                    <div class="spinner"></div>
                    <p>Subiendo imágenes...</p>
                </div>
            `;
        }
    }

    hideUploadProgress() {
        const uploadZone = this.container.querySelector('.upload-zone');
        if (uploadZone) {
            uploadZone.classList.remove('uploading');
            uploadZone.innerHTML = `
                <svg class="upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <p class="upload-text">Click para seleccionar imágenes o arrastra aquí</p>
                <p class="upload-subtext">PNG, JPG, GIF hasta 10MB</p>
            `;
        }
    }

    render() {
        const galleryContainer = this.container.querySelector('.gallery-container');
        const emptyState = this.container.querySelector('.empty-state');
        
        if (this.photos.length === 0) {
            if (galleryContainer) galleryContainer.style.display = 'none';
            if (emptyState) emptyState.style.display = 'block';
            return;
        }
        
        if (galleryContainer) galleryContainer.style.display = 'grid';
        if (emptyState) emptyState.style.display = 'none';
        
        if (galleryContainer) {
            galleryContainer.innerHTML = this.photos.map((photo, index) => 
                this.renderPhotoItem(photo, index)
            ).join('');
            
            this.attachPhotoEventListeners();
        }
    }

    renderPhotoItem(photo, index) {
        const imageUrl = photo.preview || photo.image_url || photo.path;
        const isPrimary = photo.is_primary ? 'is-primary' : '';
        
        return `
            <div class="photo-item ${isPrimary}" data-index="${index}" draggable="true">
                <div class="image-container">
                    <img src="${imageUrl}" alt="${photo.description || 'Product photo'}" class="gallery-image">
                    
                    ${photo.is_primary ? `
                        <div class="primary-badge">
                            <svg class="primary-icon" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            Principal
                        </div>
                    ` : ''}
                    
                    <div class="overlay-controls">
                        <button type="button" class="control-btn primary-btn ${photo.is_primary ? 'active' : ''}" 
                                data-action="setPrimary" data-index="${index}" title="Marcar como principal">
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </button>
                        
                        <button type="button" class="control-btn delete-btn" 
                                data-action="removePhoto" data-index="${index}" title="Eliminar foto">
                            <svg viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="drag-handle">
                        <svg viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                        </svg>
                    </div>
                </div>
                
                <div class="photo-info">
                    <input type="text" value="${photo.description || ''}" 
                           placeholder="Descripción de la imagen" 
                           class="description-input" 
                           data-action="updateDescription" 
                           data-index="${index}">
                    <div class="position-indicator">Posición: ${index + 1}</div>
                </div>
            </div>
        `;
    }

    attachPhotoEventListeners() {
        this.container.querySelectorAll('.photo-item').forEach((item, index) => {
            // Drag events
            item.addEventListener('dragstart', (e) => this.startDrag(e, index));
            item.addEventListener('dragover', (e) => this.handleDragOverPhoto(e, index));
            item.addEventListener('dragleave', (e) => this.handleDragLeavePhoto(e));
            item.addEventListener('drop', (e) => this.dropPhoto(e, index));
            
            // Button clicks
            item.querySelectorAll('[data-action]').forEach(button => {
                button.addEventListener('click', (e) => {
                    const action = e.currentTarget.dataset.action;
                    const buttonIndex = parseInt(e.currentTarget.dataset.index);
                    
                    switch (action) {
                        case 'setPrimary':
                            this.setPrimary(buttonIndex);
                            break;
                        case 'removePhoto':
                            this.removePhoto(buttonIndex);
                            break;
                    }
                });
                
                if (button.dataset.action === 'updateDescription') {
                    button.addEventListener('input', (e) => {
                        this.updateDescription(parseInt(button.dataset.index), e.target.value);
                    });
                    button.addEventListener('blur', (e) => {
                        this.updateDescription(parseInt(button.dataset.index), e.target.value);
                    });
                }
            });
        });
    }

    updateWire() {
        if (this.wire && this.wire.set && this.statePath) {
            this.wire.set(this.statePath, this.photos);
        }
    }

    setWire(wire, statePath) {
        this.wire = wire;
        this.statePath = statePath;
    }

    setProductId(productId) {
        this.options.productId = productId;
        this.loadExistingPhotos();
    }

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-message">${message}</span>
                <button class="notification-close">&times;</button>
            </div>
        `;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
        
        // Manual close
        notification.querySelector('.notification-close').addEventListener('click', () => {
            notification.remove();
        });
    }

    // Public API methods
    addPhotos(files) {
        this.processFiles(files);
    }

    getPhotos() {
        return this.photos;
    }

    getPrimaryPhoto() {
        return this.photos.find(photo => photo.is_primary);
    }

    clearGallery() {
        this.photos = [];
        this.render();
        this.updateWire();
    }
}

// Alpine.js integration function
function productPhotoGallery() {
    return {
        photos: [],
        wire: null,
        draggedIndex: null,
        gallery: null,
        productId: null,

        init(wire) {
            this.wire = wire;
            
            // Get product ID from the wire component or from data attributes
            this.productId = this.getProductId();
            
            this.gallery = new ProductPhotoGallery(this.$el, {
                productId: this.productId
            });
            this.gallery.setWire(wire, this.getStatePath());
            
            // Sync initial state
            this.photos = this.gallery.getPhotos();
        },

        getProductId() {
            // Try to get product ID from various sources
            if (this.$el.dataset.productId) {
                return this.$el.dataset.productId;
            }
            
            // Try to extract from URL or other sources
            const urlParts = window.location.pathname.split('/');
            const editIndex = urlParts.indexOf('edit');
            if (editIndex !== -1 && urlParts[editIndex + 1]) {
                return urlParts[editIndex + 1];
            }
            
            return null;
        },

        getStatePath() {
            // This should match the field name in your form
            return 'photos';
        },

        // Delegate methods to gallery instance
        handleFileSelect(event) {
            this.gallery.handleFileSelect(event);
        },

        handleDrop(event) {
            this.gallery.handleDrop(event);
        },

        setPrimary(index) {
            this.gallery.setPrimary(index);
        },

        removePhoto(index) {
            this.gallery.removePhoto(index);
        },

        updatePhoto(index, field, value) {
            if (field === 'description') {
                this.gallery.updateDescription(index, value);
            }
        },

        dragStart(event, index) {
            this.gallery.startDrag(event, index);
        },

        drop(event, index) {
            this.gallery.dropPhoto(event, index);
        }
    };
}

// Export for use in other contexts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { ProductPhotoGallery, productPhotoGallery };
}
