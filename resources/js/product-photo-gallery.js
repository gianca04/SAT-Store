/**
 * Product Photo Gallery Component
 * Handles image upload, reordering, and primary selection
 */

class ProductPhotoGallery {
    constructor(container, options = {}) {
        this.container = container;
        this.options = {
            maxFileSize: 10 * 1024 * 1024, // 10MB
            allowedTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            maxFiles: 20,
            ...options
        };
        
        this.photos = [];
        this.draggedIndex = null;
        this.wire = null;
        
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

    loadExistingPhotos() {
        // Load photos from server or wire component
        if (this.wire && this.wire.get) {
            const existingPhotos = this.wire.get(this.statePath) || [];
            this.photos = existingPhotos.map(photo => ({
                ...photo,
                id: photo.id || Date.now() + Math.random()
            }));
            this.render();
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

    processFiles(files) {
        if (this.photos.length + files.length > this.options.maxFiles) {
            this.showNotification(`Máximo ${this.options.maxFiles} imágenes permitidas`, 'error');
            return;
        }

        files.forEach((file, index) => {
            if (file.size > this.options.maxFileSize) {
                this.showNotification(`El archivo ${file.name} es demasiado grande. Máximo 10MB.`, 'error');
                return;
            }

            if (!this.options.allowedTypes.includes(file.type)) {
                this.showNotification(`Tipo de archivo no permitido: ${file.type}`, 'error');
                return;
            }

            this.createPhotoPreview(file, index);
        });
    }

    createPhotoPreview(file, index) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const newPhoto = {
                id: Date.now() + index,
                file: file,
                preview: e.target.result,
                description: '',
                is_primary: this.photos.length === 0,
                position: this.photos.length + 1,
                isNew: true
            };
            
            this.photos.push(newPhoto);
            this.render();
            this.updateWire();
            this.showNotification('Imagen añadida correctamente', 'success');
        };
        
        reader.onerror = () => {
            this.showNotification('Error al cargar la imagen', 'error');
        };
        
        reader.readAsDataURL(file);
    }

    setPrimary(index) {
        this.photos.forEach((photo, i) => {
            photo.is_primary = i === index;
        });
        this.render();
        this.updateWire();
        this.showNotification('Imagen principal actualizada', 'success');
    }

    removePhoto(index) {
        if (!confirm('¿Estás seguro de que quieres eliminar esta foto?')) {
            return;
        }

        const removedPhoto = this.photos.splice(index, 1)[0];
        
        // If removed photo was primary, set first photo as primary
        if (removedPhoto.is_primary && this.photos.length > 0) {
            this.photos[0].is_primary = true;
        }
        
        this.updatePositions();
        this.render();
        this.updateWire();
        this.showNotification('Imagen eliminada', 'info');
    }

    updateDescription(index, description) {
        if (this.photos[index]) {
            this.photos[index].description = description;
            this.updateWire();
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

    render() {
        const galleryContainer = this.container.querySelector('.gallery-container');
        const emptyState = this.container.querySelector('.empty-state');
        
        if (this.photos.length === 0) {
            galleryContainer.style.display = 'none';
            emptyState.style.display = 'block';
            return;
        }
        
        galleryContainer.style.display = 'grid';
        emptyState.style.display = 'none';
        
        galleryContainer.innerHTML = this.photos.map((photo, index) => 
            this.renderPhotoItem(photo, index)
        ).join('');
        
        this.attachPhotoEventListeners();
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
                        case 'updateDescription':
                            this.updateDescription(buttonIndex, e.target.value);
                            break;
                    }
                });
                
                if (button.dataset.action === 'updateDescription') {
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

        init(wire) {
            this.wire = wire;
            this.gallery = new ProductPhotoGallery(this.$el);
            this.gallery.setWire(wire, '{{ $getStatePath() }}');
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
        }
    };
}

// Export for use in other contexts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { ProductPhotoGallery, productPhotoGallery };
}
