<template>
  <Layout>
    <div class="min-h-screen bg-gray-50">
      <!-- Admin Header -->
      <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
              <p class="text-gray-600 mt-1">Manage your store's products and brands</p>
            </div>
            <div class="flex items-center space-x-4">
              <div class="bg-blue-50 text-blue-700 px-4 py-2 rounded-lg">
                <i class="fas fa-user-shield mr-2"></i>
                Administrator
              </div>
              <router-link 
                to="/"
                class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-4 py-2 rounded-lg font-medium transition-colors"
              >
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Store
              </router-link>
            </div>
          </div>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Navigation Tabs -->
      <div class="mb-8">
        <nav class="flex space-x-8">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              'py-2 px-1 border-b-2 font-medium text-sm',
              activeTab === tab.id
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            {{ tab.label }}
          </button>
        </nav>
      </div>

      <!-- Brands Management -->
      <div v-if="activeTab === 'brands'">
        <div class="bg-white rounded-lg shadow-md">
          <!-- Header -->
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <h2 class="text-xl font-semibold text-gray-900">Gestión de Marcas</h2>
              <button
                @click="showBrandForm = true; editingBrand = null"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
              >
                + Nueva Marca
              </button>
            </div>
          </div>

          <!-- Brands List -->
          <div class="p-6">
            <div v-if="brandsStore.isLoading" class="text-center py-8">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
              <p class="mt-2 text-gray-600">Cargando marcas...</p>
            </div>

            <div v-else class="space-y-4">
              <div
                v-for="brand in brandsStore.brands"
                :key="brand.id"
                class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50"
              >
                <div>
                  <h3 class="font-medium text-gray-900">{{ brand.name }}</h3>
                  <p class="text-sm text-gray-600">{{ brand.description || 'Sin descripción' }}</p>
                  <p class="text-xs text-gray-500 mt-1">
                    {{ brand.products_count || 0 }} producto{{ (brand.products_count || 0) !== 1 ? 's' : '' }}
                  </p>
                </div>
                <div class="flex space-x-2">
                  <button
                    @click="editBrand(brand)"
                    class="text-blue-600 hover:text-blue-700 px-3 py-1 rounded"
                  >
                    Editar
                  </button>
                  <button
                    @click="deleteBrand(brand)"
                    class="text-red-600 hover:text-red-700 px-3 py-1 rounded"
                  >
                    Eliminar
                  </button>
                </div>
              </div>

              <div v-if="brandsStore.brands.length === 0" class="text-center py-8 text-gray-500">
                No hay marcas registradas
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Products Management -->
      <div v-if="activeTab === 'products'">
        <div class="bg-white rounded-lg shadow-md">
          <!-- Header -->
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <h2 class="text-xl font-semibold text-gray-900">Gestión de Productos</h2>
              <button
                @click="showProductForm = true; editingProduct = null"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
              >
                + Nuevo Producto
              </button>
            </div>
          </div>

          <!-- Products List -->
          <div class="p-6">
            <div v-if="productsStore.isLoading" class="text-center py-8">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
              <p class="mt-2 text-gray-600">Cargando productos...</p>
            </div>

            <div v-else class="space-y-4">
              <div
                v-for="product in productsStore.products"
                :key="product.id"
                class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50"
              >
                <div class="flex-1">
                  <div class="flex items-center space-x-3">
                    <h3 class="font-medium text-gray-900">{{ product.name }}</h3>
                    <span 
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="product.active 
                        ? 'bg-green-100 text-green-800' 
                        : 'bg-red-100 text-red-800'"
                    >
                      {{ product.active ? 'Activo' : 'Inactivo' }}
                    </span>
                  </div>
                  <p class="text-sm text-gray-600 mt-1">{{ product.description || 'Sin descripción' }}</p>
                  <div class="flex items-center space-x-4 text-xs text-gray-500 mt-2">
                    <span v-if="product.brand">Marca: {{ product.brand.name }}</span>
                    <span>{{ product.photos_count || 0 }} foto{{ (product.photos_count || 0) !== 1 ? 's' : '' }}</span>
                  </div>
                </div>
                <div class="flex space-x-2">
                  <button
                    @click="editProduct(product)"
                    class="text-blue-600 hover:text-blue-700 px-3 py-1 rounded"
                  >
                    Editar
                  </button>
                  <button
                    @click="deleteProduct(product)"
                    class="text-red-600 hover:text-red-700 px-3 py-1 rounded"
                  >
                    Eliminar
                  </button>
                </div>
              </div>

              <div v-if="productsStore.products.length === 0" class="text-center py-8 text-gray-500">
                No hay productos registrados
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Photos Management -->
      <div v-if="activeTab === 'photos'">
        <div class="bg-white rounded-lg shadow-md">
          <!-- Header -->
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <h2 class="text-xl font-semibold text-gray-900">Gestión de Fotos</h2>
              <button
                @click="showPhotoForm = true; editingPhoto = null"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
              >
                + Nueva Foto
              </button>
            </div>
          </div>

          <!-- Photos List -->
          <div class="p-6">
            <div v-if="adminStore.isLoading" class="text-center py-8">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
              <p class="mt-2 text-gray-600">Cargando fotos...</p>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div
                v-for="photo in adminStore.productPhotos"
                :key="photo.id"
                class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50"
              >
                <div class="aspect-square bg-gray-100 rounded-lg mb-3 flex items-center justify-center">
                  <img 
                    v-if="photo.image_url" 
                    :src="photo.image_url" 
                    :alt="photo.description || 'Foto del producto'"
                    class="w-full h-full object-cover rounded-lg"
                  />
                  <svg v-else class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <div class="mb-3">
                  <p class="text-sm font-medium text-gray-900">{{ photo.description || 'Sin descripción' }}</p>
                  <p class="text-xs text-gray-500">Producto: {{ photo.product?.name || 'N/A' }}</p>
                </div>
                <div class="flex space-x-2">
                  <button
                    @click="editPhoto(photo)"
                    class="text-blue-600 hover:text-blue-700 text-sm px-2 py-1 rounded"
                  >
                    Editar
                  </button>
                  <button
                    @click="deletePhoto(photo)"
                    class="text-red-600 hover:text-red-700 text-sm px-2 py-1 rounded"
                  >
                    Eliminar
                  </button>
                </div>
              </div>

              <div v-if="adminStore.productPhotos.length === 0" class="col-span-full text-center py-8 text-gray-500">
                No hay fotos registradas
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Brand Form Modal -->
    <div v-if="showBrandForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-lg mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          {{ editingBrand ? 'Editar Marca' : 'Nueva Marca' }}
        </h3>
        
        <form @submit.prevent="saveBrand">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
              <input
                v-model="brandForm.name"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
              <textarea
                v-model="brandForm.description"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              ></textarea>
            </div>
            
            <!-- Imagen actual (si existe) -->
            <div v-if="editingBrand && editingBrand.image_url" class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Imagen actual</label>
              <div class="flex items-center space-x-4">
                <img :src="editingBrand.image_url" alt="Imagen actual" class="w-16 h-16 object-cover rounded" />
                <button
                  type="button"
                  @click="deleteBrandImage"
                  class="text-red-600 hover:text-red-700 text-sm"
                >
                  Eliminar imagen
                </button>
              </div>
            </div>
            
            <!-- Subida de imagen -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ editingBrand ? 'Nueva imagen' : 'Imagen de la marca' }}
              </label>
              <FileUpload
                ref="brandImageUpload"
                @upload="handleBrandImageUpload"
              />
            </div>
          </div>
          
          <div class="flex space-x-3 mt-6">
            <button
              type="submit"
              :disabled="adminStore.isLoading"
              class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 disabled:opacity-50"
            >
              {{ adminStore.isLoading ? 'Guardando...' : 'Guardar' }}
            </button>
            <button
              type="button"
              @click="closeBrandForm"
              class="flex-1 bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700"
            >
              Cancelar
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Product Form Modal -->
    <div v-if="showProductForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          {{ editingProduct ? 'Editar Producto' : 'Nuevo Producto' }}
        </h3>
        
        <form @submit.prevent="saveProduct">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
              <input
                v-model="productForm.name"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
              <textarea
                v-model="productForm.description"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              ></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
              <select
                v-model="productForm.brand_id"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="">Seleccionar marca</option>
                <option v-for="brand in brandsStore.brands" :key="brand.id" :value="brand.id">
                  {{ brand.name }}
                </option>
              </select>
            </div>
            <div>
              <label class="flex items-center">
                <input
                  v-model="productForm.active"
                  type="checkbox"
                  class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                >
                <span class="ml-2 text-sm text-gray-700">Producto activo</span>
              </label>
            </div>
          </div>
          
          <div class="flex space-x-3 mt-6">
            <button
              type="submit"
              :disabled="adminStore.isLoading"
              class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 disabled:opacity-50"
            >
              {{ adminStore.isLoading ? 'Guardando...' : 'Guardar' }}
            </button>
            <button
              type="button"
              @click="closeProductForm"
              class="flex-1 bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700"
            >
              Cancelar
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Photo Form Modal -->
    <div v-if="showPhotoForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-lg mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          {{ editingPhoto ? 'Editar Foto' : 'Nueva Foto' }}
        </h3>
        
        <form @submit.prevent="savePhoto">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Producto</label>
              <select
                v-model="photoForm.product_id"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="">Seleccionar producto</option>
                <option v-for="product in productsStore.products" :key="product.id" :value="product.id">
                  {{ product.name }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
              <textarea
                v-model="photoForm.description"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              ></textarea>
            </div>
            
            <!-- Imagen actual (si existe) -->
            <div v-if="editingPhoto && editingPhoto.image_url" class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Imagen actual</label>
              <div class="flex items-center space-x-4">
                <img :src="editingPhoto.image_url" alt="Imagen actual" class="w-16 h-16 object-cover rounded" />
                <button
                  type="button"
                  @click="deletePhotoImage"
                  class="text-red-600 hover:text-red-700 text-sm"
                >
                  Eliminar imagen
                </button>
              </div>
            </div>
            
            <!-- Subida de imagen -->
            <div v-if="!editingPhoto">
              <label class="block text-sm font-medium text-gray-700 mb-2">Imagen del producto</label>
              <FileUpload
                ref="productPhotoUpload"
                @upload="handleProductPhotoUpload"
              />
            </div>
          </div>
          
          <div class="flex space-x-3 mt-6">
            <button
              type="submit"
              :disabled="adminStore.isLoading"
              class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 disabled:opacity-50"
            >
              {{ adminStore.isLoading ? 'Guardando...' : 'Guardar' }}
            </button>
            <button
              type="button"
              @click="closePhotoForm"
              class="flex-1 bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700"
            >
              Cancelar
            </button>
          </div>
        </form>
      </div>
    </div>
    </div>
  </Layout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import Layout from '@/components/layout/Layout.vue'
import { useBrandsStore } from '@/stores/brands'
import { useProductsStore } from '@/stores/products'
import { useAdminStore } from '@/stores/admin'
import { adminApi } from '@/services/api'
import FileUpload from '@/components/FileUpload.vue'
import type { Brand, Product, ProductPhoto } from '@/types'

// Stores
const brandsStore = useBrandsStore()
const productsStore = useProductsStore()
const adminStore = useAdminStore()

// State
const activeTab = ref('brands')
const showBrandForm = ref(false)
const showProductForm = ref(false)
const showPhotoForm = ref(false)
const editingBrand = ref<Brand | null>(null)
const editingProduct = ref<Product | null>(null)
const editingPhoto = ref<ProductPhoto | null>(null)

// Component refs
const brandImageUpload = ref<InstanceType<typeof FileUpload>>()
const productPhotoUpload = ref<InstanceType<typeof FileUpload>>()

// Form data
const brandForm = ref({
  name: '',
  description: ''
})

const productForm = ref({
  name: '',
  description: '',
  brand_id: '',
  active: true
})

const photoForm = ref({
  product_id: '',
  description: ''
})

// Tabs configuration
const tabs = [
  { id: 'brands', label: 'Marcas' },
  { id: 'products', label: 'Productos' },
  { id: 'photos', label: 'Fotos' }
]

// Brand methods
const editBrand = (brand: Brand) => {
  editingBrand.value = brand
  brandForm.value = {
    name: brand.name,
    description: brand.description || ''
  }
  showBrandForm.value = true
}

const saveBrand = async () => {
  try {
    if (editingBrand.value) {
      await adminStore.updateBrand(editingBrand.value.id, brandForm.value)
    } else {
      await adminStore.createBrand(brandForm.value)
    }
    await brandsStore.fetchBrands()
    closeBrandForm()
  } catch (error) {
    console.error('Error saving brand:', error)
  }
}

const deleteBrand = async (brand: Brand) => {
  if (confirm(`¿Estás seguro de que quieres eliminar la marca "${brand.name}"?`)) {
    try {
      await adminStore.deleteBrand(brand.id)
      await brandsStore.fetchBrands()
    } catch (error) {
      console.error('Error deleting brand:', error)
    }
  }
}

const closeBrandForm = () => {
  showBrandForm.value = false
  editingBrand.value = null
  brandForm.value = {
    name: '',
    description: ''
  }
  brandImageUpload.value?.clearFile()
}

// Image upload methods
const handleBrandImageUpload = async (file: File) => {
  if (!editingBrand.value && !brandForm.value.name) {
    brandImageUpload.value?.setError('Debe guardar la marca primero antes de subir una imagen')
    return
  }

  try {
    brandImageUpload.value?.setUploading(true)
    
    // Si estamos editando una marca existente
    if (editingBrand.value) {
      const result = await adminApi.uploadBrandImage(editingBrand.value.id, file)
      brandImageUpload.value?.setSuccess('Imagen subida exitosamente')
      
      // Actualizar la imagen en la marca actual
      editingBrand.value.image_url = result.data.url
      editingBrand.value.foto_path = result.data.path
      
      // Recargar las marcas para actualizar la lista
      await brandsStore.fetchBrands()
    } else {
      brandImageUpload.value?.setError('Debe guardar la marca primero')
    }
  } catch (error) {
    console.error('Error uploading image:', error)
    brandImageUpload.value?.setError('Error al subir la imagen')
  } finally {
    brandImageUpload.value?.setUploading(false)
  }
}

const deleteBrandImage = async () => {
  if (!editingBrand.value) return
  
  if (confirm('¿Estás seguro de que quieres eliminar esta imagen?')) {
    try {
      await adminApi.deleteBrandImage(editingBrand.value.id)
      editingBrand.value.image_url = null
      editingBrand.value.foto_path = null
      await brandsStore.fetchBrands()
    } catch (error) {
      console.error('Error deleting image:', error)
    }
  }
}

// Product methods
const editProduct = (product: Product) => {
  editingProduct.value = product
  productForm.value = {
    name: product.name,
    description: product.description || '',
    brand_id: product.brand_id.toString(),
    active: product.active
  }
  showProductForm.value = true
}

const saveProduct = async () => {
  try {
    const data = {
      ...productForm.value,
      brand_id: Number(productForm.value.brand_id)
    }
    
    if (editingProduct.value) {
      await adminStore.updateProduct(editingProduct.value.id, data)
    } else {
      await adminStore.createProduct(data)
    }
    await productsStore.fetchProducts()
    closeProductForm()
  } catch (error) {
    console.error('Error saving product:', error)
  }
}

const deleteProduct = async (product: Product) => {
  if (confirm(`¿Estás seguro de que quieres eliminar el producto "${product.name}"?`)) {
    try {
      await adminStore.deleteProduct(product.id)
      await productsStore.fetchProducts()
    } catch (error) {
      console.error('Error deleting product:', error)
    }
  }
}

const closeProductForm = () => {
  showProductForm.value = false
  editingProduct.value = null
  productForm.value = {
    name: '',
    description: '',
    brand_id: '',
    active: true
  }
}

// Photo methods
const editPhoto = (photo: ProductPhoto) => {
  editingPhoto.value = photo
  photoForm.value = {
    product_id: photo.product_id.toString(),
    description: photo.description || ''
  }
  showPhotoForm.value = true
}

const savePhoto = async () => {
  try {
    const data = {
      ...photoForm.value,
      product_id: Number(photoForm.value.product_id)
    }
    
    if (editingPhoto.value) {
      await adminStore.updateProductPhoto(editingPhoto.value.id, data)
    } else {
      await adminStore.createProductPhoto(data)
    }
    await adminStore.fetchProductPhotos()
    closePhotoForm()
  } catch (error) {
    console.error('Error saving photo:', error)
  }
}

const deletePhoto = async (photo: ProductPhoto) => {
  if (confirm('¿Estás seguro de que quieres eliminar esta foto?')) {
    try {
      await adminStore.deleteProductPhoto(photo.id)
      await adminStore.fetchProductPhotos()
    } catch (error) {
      console.error('Error deleting photo:', error)
    }
  }
}

const closePhotoForm = () => {
  showPhotoForm.value = false
  editingPhoto.value = null
  photoForm.value = {
    product_id: '',
    description: ''
  }
  productPhotoUpload.value?.clearFile()
}

// Product photo upload methods
const handleProductPhotoUpload = async (file: File) => {
  if (!photoForm.value.product_id) {
    productPhotoUpload.value?.setError('Debe seleccionar un producto primero')
    return
  }

  try {
    productPhotoUpload.value?.setUploading(true)
    
    const productId = Number(photoForm.value.product_id)
    const result = await adminApi.uploadProductPhoto(productId, file, photoForm.value.description)
    
    productPhotoUpload.value?.setSuccess('Foto subida exitosamente')
    
    // Recargar las fotos para actualizar la lista
    await adminStore.fetchProductPhotos()
    
    // Cerrar el formulario después de la subida exitosa
    closePhotoForm()
  } catch (error) {
    console.error('Error uploading photo:', error)
    productPhotoUpload.value?.setError('Error al subir la foto')
  } finally {
    productPhotoUpload.value?.setUploading(false)
  }
}

const deletePhotoImage = async () => {
  if (!editingPhoto.value) return
  
  if (confirm('¿Estás seguro de que quieres eliminar esta foto?')) {
    try {
      await adminApi.deleteProductPhotoFile(editingPhoto.value.id)
      await adminStore.fetchProductPhotos()
      closePhotoForm()
    } catch (error) {
      console.error('Error deleting photo:', error)
    }
  }
}

// Lifecycle
onMounted(async () => {
  await Promise.all([
    brandsStore.fetchBrands(),
    productsStore.fetchProducts(),
    adminStore.fetchProductPhotos()
  ])
})
</script>
