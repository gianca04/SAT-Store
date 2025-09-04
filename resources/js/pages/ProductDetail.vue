<template>
  <div class="container mx-auto px-4 py-8">
    <div v-if="isLoading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
      <p class="mt-4 text-gray-600">Cargando producto...</p>
    </div>

    <div v-else-if="hasError" class="text-center py-12">
      <div class="text-red-600 mb-4">
        <h3 class="text-lg font-medium">Error al cargar el producto</h3>
        <p class="text-gray-600">{{ error }}</p>
      </div>
      <router-link 
        to="/products"
        class="inline-block px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
      >
        Volver a productos
      </router-link>
    </div>

    <div v-else-if="product">
      <!-- Breadcrumb -->
      <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
          <li><router-link to="/" class="hover:text-blue-600">Inicio</router-link></li>
          <li class="before:content-['/'] before:mx-2">
            <router-link to="/products" class="hover:text-blue-600">Productos</router-link>
          </li>
          <li class="before:content-['/'] before:mx-2 text-gray-900 font-medium">{{ product.name }}</li>
        </ol>
      </nav>

      <!-- Product Header -->
      <div class="bg-white rounded-lg shadow-md p-8 mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Product Info -->
          <div class="lg:col-span-2">
            <div class="flex items-center space-x-4 mb-4">
              <h1 class="text-4xl font-bold text-gray-900">{{ product.name }}</h1>
              <span 
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                :class="product.active 
                  ? 'bg-green-100 text-green-800' 
                  : 'bg-red-100 text-red-800'"
              >
                {{ product.active ? 'Disponible' : 'No Disponible' }}
              </span>
            </div>

            <!-- Brand -->
            <div class="mb-6">
              <router-link 
                v-if="product.brand"
                :to="`/brands/${product.brand.id}`"
                class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                {{ product.brand.name }}
              </router-link>
            </div>

            <!-- Description -->
            <div class="mb-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-3">Descripción</h2>
              <p class="text-gray-600 leading-relaxed">
                {{ product.description || 'Sin descripción disponible para este producto.' }}
              </p>
            </div>

            <!-- Product Details -->
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div class="bg-gray-50 p-4 rounded-lg">
                <dt class="font-medium text-gray-900">Fecha de creación</dt>
                <dd class="text-gray-600">{{ formatDate(product.created_at) }}</dd>
              </div>
              <div class="bg-gray-50 p-4 rounded-lg">
                <dt class="font-medium text-gray-900">Última actualización</dt>
                <dd class="text-gray-600">{{ formatDate(product.updated_at) }}</dd>
              </div>
            </div>
          </div>

          <!-- Stats Sidebar -->
          <div class="lg:col-span-1">
            <div class="bg-gray-50 rounded-lg p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Información</h3>
              
              <div class="space-y-4">
                <div class="flex items-center justify-between">
                  <span class="text-gray-600">Estado</span>
                  <span class="font-medium" :class="product.active ? 'text-green-600' : 'text-red-600'">
                    {{ product.active ? 'Activo' : 'Inactivo' }}
                  </span>
                </div>
                
                <div class="flex items-center justify-between">
                  <span class="text-gray-600">Fotos</span>
                  <span class="font-medium text-gray-900">{{ product.photos?.length || 0 }}</span>
                </div>
                
                <div v-if="product.brand" class="flex items-center justify-between">
                  <span class="text-gray-600">Marca</span>
                  <span class="font-medium text-blue-600">{{ product.brand.name }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Photos Section -->
      <div v-if="product.photos && product.photos.length > 0" class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Galería de Fotos</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="photo in product.photos"
            :key="photo.id"
            class="bg-white rounded-lg shadow-md p-4"
          >
            <div class="aspect-square bg-gray-100 rounded-lg mb-3 flex items-center justify-center">
              <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </div>
            <p class="text-sm text-gray-600">
              {{ photo.description || 'Sin descripción' }}
            </p>
            <p class="text-xs text-gray-500 mt-1">
              {{ formatDate(photo.created_at) }}
            </p>
          </div>
        </div>
      </div>

      <!-- No Photos -->
      <div v-else class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Galería de Fotos</h2>
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
          <div class="text-gray-400 mb-4">
            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900">No hay fotos disponibles</h3>
            <p class="text-gray-600">Este producto aún no tiene fotos en la galería.</p>
          </div>
        </div>
      </div>

      <!-- Related Products -->
      <div v-if="product.brand && relatedProducts.length > 0" class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
          Otros productos de {{ product.brand.name }}
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <router-link
            v-for="relatedProduct in relatedProducts"
            :key="relatedProduct.id"
            :to="`/products/${relatedProduct.id}`"
            class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-all duration-200 group border border-gray-200"
          >
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                {{ relatedProduct.name }}
              </h3>
              <span 
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                :class="relatedProduct.active 
                  ? 'bg-green-100 text-green-800' 
                  : 'bg-gray-100 text-gray-800'"
              >
                {{ relatedProduct.active ? 'Activo' : 'Inactivo' }}
              </span>
            </div>
            
            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
              {{ relatedProduct.description || 'Sin descripción disponible' }}
            </p>
            
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-500">
                {{ relatedProduct.photos_count || 0 }} foto{{ (relatedProduct.photos_count || 0) !== 1 ? 's' : '' }}
              </span>
              <span class="text-blue-600 group-hover:text-blue-700 font-medium text-sm">
                Ver detalles →
              </span>
            </div>
          </router-link>
        </div>
      </div>

      <!-- Back Button -->
      <div class="text-center">
        <router-link 
          to="/products"
          class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          Volver a productos
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useProductsStore } from '@/stores/products'

const route = useRoute()
const productsStore = useProductsStore()

// Computed
const product = computed(() => productsStore.currentProduct)
const isLoading = computed(() => productsStore.isLoading)
const hasError = computed(() => productsStore.hasError)
const error = computed(() => productsStore.error)

// Related products from the same brand
const relatedProducts = computed(() => {
  if (!product.value?.brand_id) return []
  
  return productsStore.products
    .filter(p => p.brand_id === product.value?.brand_id && p.id !== product.value?.id)
    .slice(0, 3) // Limitar a 3 productos relacionados
})

// Methods
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Lifecycle
onMounted(async () => {
  const productId = Number(route.params.id)
  if (productId) {
    await productsStore.fetchProduct(productId)
    
    // Cargar productos relacionados si tenemos la marca
    if (product.value?.brand_id) {
      await productsStore.fetchProducts({ brand_id: product.value.brand_id, per_page: 10 })
    }
  }
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
