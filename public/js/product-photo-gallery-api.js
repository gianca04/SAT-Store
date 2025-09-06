function productPhotoGallery() {
    return {
        photos: [],
        uploadProgress: false,
        authToken: null,
        productId: null,
        draggedIndex: null,
        
        // Initialize the component
        init(wire) {
            this.wire = wire;
            this.productId = this.$el.dataset.productId;
            this.setupAuthentication();
            this.loadPhotos();
        },

        // Setup authentication
        async setupAuthentication() {
            try {
                // Login with the credentials
                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        email: 'sistemas@sat-industriales.pe',
                        password: '2004Febrero.'
                    })
                });

                const data = await response.json();
                if (data.success) {
                    this.authToken = data.data.token;
                    console.log('Autenticación exitosa');
                } else {
                    console.error('Error de autenticación:', data);
                }
            } catch (error) {
                console.error('Error al autenticar:', error);
            }
        },

        // Load existing photos from API
        async loadPhotos() {
            if (!this.productId) return;

            try {
                const response = await this.apiRequest(`/api/admin/products/${this.productId}/photos`, 'GET');
                
                if (response.success && response.data) {
                    this.photos = response.data.map(photo => ({
                        id: photo.id,
                        image_url: photo.image_url,
                        description: photo.description || '',
                        is_primary: photo.is_primary,
                        position: photo.position,
                        path: photo.path
                    }));
                }
            } catch (error) {
                console.error('Error al cargar fotos:', error);
                this.showNotification('Error al cargar las fotos', 'error');
            }
        },

        // Make authenticated API requests
        async apiRequest(url, method = 'GET', body = null) {
            const headers = {
                'Accept': 'application/json',
                'Authorization': `Bearer ${this.authToken}`
            };

            const options = {
                method,
                headers
            };

            if (body) {
                if (body instanceof FormData) {
                    // Don't set Content-Type for FormData, let browser set it
                    options.body = body;
                } else {
                    headers['Content-Type'] = 'application/json';
                    options.body = JSON.stringify(body);
                }
            }

            const response = await fetch(url, options);
            return await response.json();
        },

        // Handle file selection
        handleFileSelect(event) {
            const files = Array.from(event.target.files);
            this.uploadFiles(files);
        },

        // Handle drag and drop
        handleDrop(event) {
            const files = Array.from(event.dataTransfer.files);
            this.uploadFiles(files);
        },

        // Upload files to API
        async uploadFiles(files) {
            if (!this.authToken || !this.productId) {
                this.showNotification('Error: No se pudo autenticar o no hay producto seleccionado', 'error');
                return;
            }

            if (files.length === 0) return;

            this.uploadProgress = true;

            try {
                const formData = new FormData();
                
                // Add files to FormData
                files.forEach((file, index) => {
                    formData.append('photos[]', file);
                    formData.append(`descriptions[${index}]`, '');
                });

                const response = await fetch(`/api/admin/products/${this.productId}/photos`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${this.authToken}`,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    this.showNotification(data.message, 'success');
                    await this.loadPhotos(); // Reload photos
                } else {
                    this.showNotification(data.message || 'Error al subir las fotos', 'error');
                }
            } catch (error) {
                console.error('Error al subir archivos:', error);
                this.showNotification('Error al subir las fotos', 'error');
            } finally {
                this.uploadProgress = false;
            }
        },

        // Set photo as primary
        async setPrimary(index) {
            if (!this.authToken || !this.productId) return;

            const photo = this.photos[index];
            if (!photo.id) return;

            try {
                const response = await this.apiRequest(
                    `/api/admin/products/${this.productId}/photos/${photo.id}/primary`,
                    'PUT'
                );

                if (response.success) {
                    // Update local state
                    this.photos.forEach((p, i) => {
                        p.is_primary = i === index;
                    });
                    this.showNotification('Foto principal actualizada', 'success');
                } else {
                    this.showNotification(response.message || 'Error al marcar como principal', 'error');
                }
            } catch (error) {
                console.error('Error al marcar como principal:', error);
                this.showNotification('Error al marcar como principal', 'error');
            }
        },

        // Remove photo
        async removePhoto(index) {
            if (!this.authToken || !this.productId) return;

            const photo = this.photos[index];
            if (!photo.id) {
                // If it's a new photo not yet saved, just remove from array
                this.photos.splice(index, 1);
                return;
            }

            if (!confirm('¿Estás seguro de que quieres eliminar esta foto?')) {
                return;
            }

            try {
                const response = await this.apiRequest(
                    `/api/admin/products/${this.productId}/photos/${photo.id}`,
                    'DELETE'
                );

                if (response.success) {
                    this.photos.splice(index, 1);
                    this.showNotification('Foto eliminada correctamente', 'success');
                    
                    // If there's a new primary photo, update it
                    if (response.data && response.data.new_primary) {
                        const newPrimary = response.data.new_primary;
                        const newPrimaryIndex = this.photos.findIndex(p => p.id === newPrimary.id);
                        if (newPrimaryIndex !== -1) {
                            this.photos[newPrimaryIndex].is_primary = true;
                        }
                    }
                } else {
                    this.showNotification(response.message || 'Error al eliminar la foto', 'error');
                }
            } catch (error) {
                console.error('Error al eliminar foto:', error);
                this.showNotification('Error al eliminar la foto', 'error');
            }
        },

        // Update photo description
        async updatePhoto(index, field, value) {
            const photo = this.photos[index];
            photo[field] = value;

            // If the photo has an ID, update it immediately via API
            if (photo.id && this.authToken) {
                try {
                    const updateData = {};
                    updateData[field] = value;

                    await this.apiRequest(
                        `/api/admin/product-photos/${photo.id}`,
                        'PUT',
                        updateData
                    );
                } catch (error) {
                    console.error('Error al actualizar foto:', error);
                }
            }
        },

        // Save gallery order and settings
        async saveGallery() {
            if (!this.authToken || !this.productId) return;

            try {
                const photosData = this.photos.map((photo, index) => ({
                    id: photo.id,
                    position: index + 1,
                    is_primary: photo.is_primary,
                    description: photo.description || ''
                }));

                const response = await this.apiRequest(
                    `/api/admin/products/${this.productId}/photos/gallery`,
                    'PUT',
                    { photos: photosData }
                );

                if (response.success) {
                    this.showNotification('Galería actualizada correctamente', 'success');
                } else {
                    this.showNotification(response.message || 'Error al actualizar la galería', 'error');
                }
            } catch (error) {
                console.error('Error al guardar galería:', error);
                this.showNotification('Error al guardar la galería', 'error');
            }
        },

        // Drag and drop functionality
        dragStart(event, index) {
            this.draggedIndex = index;
            event.dataTransfer.effectAllowed = 'move';
        },

        drop(event, dropIndex) {
            if (this.draggedIndex === null || this.draggedIndex === dropIndex) return;

            const draggedPhoto = this.photos[this.draggedIndex];
            this.photos.splice(this.draggedIndex, 1);
            this.photos.splice(dropIndex, 0, draggedPhoto);

            this.draggedIndex = null;

            // Auto-save the new order
            this.saveGallery();
        },

        // Show notification (you can integrate with your notification system)
        showNotification(message, type = 'info') {
            // For now, just use console and alert
            console.log(`${type.toUpperCase()}: ${message}`);
            
            // You can replace this with your preferred notification system
            if (type === 'error') {
                alert(`Error: ${message}`);
            } else if (type === 'success') {
                // You might want to show a success toast instead
                console.log(`Success: ${message}`);
            }

            // If you're using Filament notifications:
            // window.Filament?.notifications?.send({
            //     title: type === 'success' ? 'Éxito' : 'Error',
            //     message: message,
            //     status: type === 'success' ? 'success' : 'danger',
            // });
        }
    };
}
