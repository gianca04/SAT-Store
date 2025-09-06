/**
 * Product Photo Gallery Component
 * Manages photo upload, reordering, and gallery operations for products
 * Integrates with Laravel API endpoints with authentication
 */
function productPhotoGallery() {
    return {
        // Component state
        photos: [],
        uploadProgress: false,
        authToken: null,
        productId: null,
        draggedIndex: null,
        isInitialized: false,

        /**
         * Initialize the component
         */
        async init(wire) {
            this.wire = wire;
            this.productId = this.$el.dataset.productId;
            
            if (!this.productId) {
                console.warn('Product ID not found. Gallery functionality will be limited.');
                return;
            }

            // Setup global reference for event handlers
            window.productPhotoGalleryInstance = this;
            
            // Initialize authentication and load photos
            await this.setupAuthentication();
            if (this.authToken) {
                await this.loadPhotos();
                this.isInitialized = true;
            }
        },

        /**
         * Setup authentication with the API
         */
        async setupAuthentication() {
            try {
                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    },
                    body: JSON.stringify({
                        email: 'sistemas@sat-industriales.pe',
                        password: '2004Febrero.'
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    this.authToken = data.data.token;
                    console.log('Authentication successful');
                } else {
                    console.error('Authentication failed:', data.message);
                    this.showNotification('Error de autenticación', 'error');
                }
            } catch (error) {
                console.error('Authentication error:', error);
                this.showNotification('Error de conexión durante la autenticación', 'error');
            }
        },

        /**
         * Load existing photos from API
         */
        async loadPhotos() {
            if (!this.productId || !this.authToken) return;

            try {
                const response = await this.apiRequest(`/api/admin/products/${this.productId}/photos`);
                
                if (response.success) {
                    this.photos = response.data || [];
                    this.updateWire();
                    console.log(`Loaded ${this.photos.length} photos`);
                }
            } catch (error) {
                console.error('Error loading photos:', error);
                this.showNotification('Error al cargar las fotos existentes', 'error');
            }
        },

        /**
         * Make authenticated API requests
         */
        async apiRequest(url, method = 'GET', body = null) {
            if (!this.authToken) {
                throw new Error('No authentication token available');
            }

            const config = {
                method,
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${this.authToken}`,
                    'X-CSRF-TOKEN': this.getCsrfToken()
                }
            };

            if (body) {
                if (body instanceof FormData) {
                    // Don't set Content-Type for FormData, let browser handle it
                    config.body = body;
                } else {
                    config.headers['Content-Type'] = 'application/json';
                    config.body = JSON.stringify(body);
                }
            }

            const response = await fetch(url, config);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            return await response.json();
        },

        /**
         * Handle file selection from input
         */
        handleFileSelect(event) {
            const files = Array.from(event.target.files);
            this.processFiles(files);
            // Reset input to allow selecting the same files again
            event.target.value = '';
        },

        /**
         * Handle drag over event
         */
        handleDragOver(event) {
            event.preventDefault();
            event.stopPropagation();
            event.currentTarget.classList.add('dragover');
        },

        /**
         * Handle drag leave event
         */
        handleDragLeave(event) {
            event.preventDefault();
            event.stopPropagation();
            event.currentTarget.classList.remove('dragover');
        },

        /**
         * Handle drop event
         */
        handleDrop(event) {
            event.preventDefault();
            event.stopPropagation();
            event.currentTarget.classList.remove('dragover');
            
            const files = Array.from(event.dataTransfer.files);
            this.processFiles(files);
        },

        /**
         * Process and upload files
         */
        async processFiles(files) {
            if (!files.length) return;

            if (!this.authToken) {
                this.showNotification('Debe autenticarse primero', 'error');
                return;
            }

            // Validate files
            const validFiles = files.filter(file => this.validateFile(file));
            
            if (!validFiles.length) {
                this.showNotification('No hay archivos válidos para subir', 'error');
                return;
            }

            this.uploadProgress = true;

            try {
                const formData = new FormData();
                
                // Add files to form data
                validFiles.forEach((file, index) => {
                    formData.append('photos[]', file);
                    formData.append(`descriptions[${index}]`, '');
                });

                const response = await this.apiRequest(
                    `/api/admin/products/${this.productId}/photos`, 
                    'POST', 
                    formData
                );

                if (response.success) {
                    this.showNotification(response.message, 'success');
                    await this.loadPhotos();
                } else {
                    this.showNotification(response.message || 'Error al subir fotos', 'error');
                }
            } catch (error) {
                console.error('Upload error:', error);
                this.showNotification('Error al subir las fotos', 'error');
            } finally {
                this.uploadProgress = false;
            }
        },

        /**
         * Validate individual file
         */
        validateFile(file) {
            // Check if it's an image
            if (!file.type.startsWith('image/')) {
                this.showNotification(`${file.name} no es un archivo de imagen válido`, 'error');
                return false;
            }

            // Check file size (10MB limit)
            const maxSize = 10 * 1024 * 1024;
            if (file.size > maxSize) {
                this.showNotification(`${file.name} es demasiado grande (máximo 10MB)`, 'error');
                return false;
            }

            return true;
        },

        /**
         * Set photo as primary
         */
        async setPrimary(photoId) {
            if (!this.authToken) return;

            try {
                const response = await this.apiRequest(
                    `/api/admin/products/${this.productId}/photos/${photoId}/primary`, 
                    'PUT'
                );

                if (response.success) {
                    this.showNotification('Foto principal actualizada', 'success');
                    await this.loadPhotos();
                } else {
                    this.showNotification(response.message || 'Error al marcar como principal', 'error');
                }
            } catch (error) {
                console.error('Error setting primary photo:', error);
                this.showNotification('Error al marcar como principal', 'error');
            }
        },

        /**
         * Delete photo
         */
        async deletePhoto(photoId) {
            if (!confirm('¿Estás seguro de que quieres eliminar esta foto?')) {
                return;
            }

            if (!this.authToken) return;

            try {
                const response = await this.apiRequest(
                    `/api/admin/products/${this.productId}/photos/${photoId}`, 
                    'DELETE'
                );

                if (response.success) {
                    this.showNotification('Foto eliminada correctamente', 'success');
                    await this.loadPhotos();
                } else {
                    this.showNotification(response.message || 'Error al eliminar foto', 'error');
                }
            } catch (error) {
                console.error('Error deleting photo:', error);
                this.showNotification('Error al eliminar foto', 'error');
            }
        },

        /**
         * Update photo description
         */
        async updateDescription(photoId, description) {
            if (!this.authToken) return;

            try {
                const response = await this.apiRequest(
                    `/api/admin/product-photos/${photoId}`, 
                    'PUT', 
                    { description }
                );

                if (response.success) {
                    this.showNotification('Descripción actualizada', 'success');
                    // Update local data
                    const photo = this.photos.find(p => p.id === photoId);
                    if (photo) {
                        photo.description = description;
                        this.updateWire();
                    }
                } else {
                    this.showNotification(response.message || 'Error al actualizar descripción', 'error');
                }
            } catch (error) {
                console.error('Error updating description:', error);
                this.showNotification('Error al actualizar descripción', 'error');
            }
        },

        /**
         * Save complete gallery changes
         */
        async saveGallery() {
            if (!this.authToken || !this.photos.length) return;

            try {
                const response = await this.apiRequest(
                    `/api/admin/products/${this.productId}/photos/gallery`, 
                    'PUT',
                    {
                        photos: this.photos.map(photo => ({
                            id: photo.id,
                            position: photo.position,
                            is_primary: photo.is_primary,
                            description: photo.description || ''
                        }))
                    }
                );

                if (response.success) {
                    this.showNotification('Galería guardada correctamente', 'success');
                } else {
                    this.showNotification(response.message || 'Error al guardar galería', 'error');
                }
            } catch (error) {
                console.error('Error saving gallery:', error);
                this.showNotification('Error al guardar la galería', 'error');
            }
        },

        /**
         * Drag and drop reordering
         */
        startDrag(index) {
            this.draggedIndex = index;
        },

        dragOver(event, index) {
            event.preventDefault();
            if (this.draggedIndex !== null && this.draggedIndex !== index) {
                event.currentTarget.style.borderTop = '2px solid #3b82f6';
            }
        },

        dragLeave(event) {
            event.currentTarget.style.borderTop = '';
        },

        async dropPhoto(event, targetIndex) {
            event.preventDefault();
            event.currentTarget.style.borderTop = '';

            if (this.draggedIndex === null || this.draggedIndex === targetIndex) {
                this.draggedIndex = null;
                return;
            }

            // Reorder photos array
            const draggedPhoto = this.photos[this.draggedIndex];
            this.photos.splice(this.draggedIndex, 1);
            this.photos.splice(targetIndex, 0, draggedPhoto);

            // Update positions
            this.photos.forEach((photo, index) => {
                photo.position = index + 1;
            });

            this.draggedIndex = null;
            this.updateWire();
            
            // Auto-save after reordering
            await this.saveGallery();
        },

        /**
         * Update Livewire component with current photos
         */
        updateWire() {
            if (this.wire && this.photos) {
                const photoData = this.photos.map(photo => ({
                    id: photo.id,
                    path: photo.path,
                    description: photo.description,
                    is_primary: photo.is_primary,
                    position: photo.position,
                    image_url: photo.image_url
                }));
                this.wire.set('photos', photoData);
            }
        },

        /**
         * Get CSRF token from meta tag
         */
        getCsrfToken() {
            const token = document.querySelector('meta[name="csrf-token"]');
            return token ? token.getAttribute('content') : '';
        },

        /**
         * Show notification to user
         */
        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-md shadow-lg text-white transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                'bg-blue-500'
            }`;
            notification.textContent = message;

            // Add to DOM
            document.body.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
                notification.style.opacity = '1';
            }, 10);

            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }
    };
}

// Global instance for event handlers
window.productPhotoGalleryInstance = null;