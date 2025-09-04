<template>
  <div class="container mx-auto px-4 py-8">
    <div v-if="isLoading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
      <p class="mt-4 text-gray-600">Cargando marca...</p>
    </div>

    <div v-else-if="hasError" class="text-center py-12">
      <div class="text-red-600 mb-4">
        <h3 class="text-lg font-medium">Error al cargar la marca</h3>
        <p class="text-gray-600">{{ error }}</p>
      </div>
      <router-link 
        to="/brands"
        class="inline-block px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
      >
        Volver a marcas
      </router-link>
    </div>

    <div v-else-if="brand">
      <!-- Breadcrumb -->
      <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
          <li><router-link to="/" class="hover:text-blue-600">Inicio</router-link></li>
          <li class="before:content-['/'] before:mx-2">
            <router-link to="/brands" class="hover:text-blue-600">Marcas</router-link>
          </li>
          <li class="before:content-['/'] before:mx-2 text-gray-900 font-medium">{{ brand.name }}</li>
        </ol>
      </nav>

      <!-- Brand Header -->
      <div class="bg-white rounded-lg shadow-md p-8 mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
          <div class="flex-1">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ brand.name }}</h1>
            <p class="text-lg text-gray-600 mb-6 max-w-3xl">
              {{ brand.description || 'Sin descripción disponible' }}
            </p>
            <div class="flex items-center space-x-6 text-sm text-gray-500">
              <span>Creada: {{ formatDate(brand.created_at) }}</span>
              <span>Actualizada: {{ formatDate(brand.updated_at) }}</span>
            </div>
          </div>
          <div class="mt-6 lg:mt-0 lg:ml-8">
            <div class="text-center">
              <div class="text-3xl font-bold text-blue-600">{{ brand.products?.length || 0 }}</div>
              <div class="text-gray-600">Productos Disponibles</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Products Section -->
      <div>
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold text-gray-900">Productos de {{ brand.name }}</h2>
          <div class="text-sm text-gray-600">
            Mostrando {{ brand.products?.length || 0 }} productos
          </div>
        </div>

        <!-- No products -->
        <div v-if="!brand.products || brand.products.length === 0" class="text-center py-12 bg-white rounded-lg shadow-md">
          <div class="text-gray-400 mb-4">
            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900">No hay productos disponibles</h3>
            <p class="text-gray-600">Esta marca aún no tiene productos activos en el catálogo.</p>
          </div>
        </div>

        <!-- Products Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <router-link
            v-for="product in brand.products"
            :key="product.id"
            :to="`/products/${product.id}`"
            class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-200 group border border-gray-200"
          >
            <div class="p-6">
              <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                  {{ product.name }}
                </h3>
                <span 
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="product.active 
                    ? 'bg-green-100 text-green-800' 
                    : 'bg-gray-100 text-gray-800'"
                >
                  {{ product.active ? 'Activo' : 'Inactivo' }}
                </span>
              </div>
              
              <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                {{ product.description || 'Sin descripción disponible' }}
              </p>
              
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">
                  {{ product.photos?.length || 0 }} foto{{ (product.photos?.length || 0) !== 1 ? 's' : '' }}
                </span>
                <span class="text-blue-600 group-hover:text-blue-700 font-medium text-sm">
                  Ver detalles →
                </span>
              </div>
            </div>
          </router-link>
        </div>
      </div>

      <!-- Back Button -->
      <div class="mt-8 text-center">
        <router-link 
          to="/brands"
          class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          Volver a marcas
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useBrandsStore } from '@/stores/brands'

const route = useRoute()
const brandsStore = useBrandsStore()

// Computed
const brand = computed(() => brandsStore.currentBrand)
const isLoading = computed(() => brandsStore.isLoading)
const hasError = computed(() => brandsStore.hasError)
const error = computed(() => brandsStore.error)

// Methods
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Lifecycle
onMounted(async () => {
  const brandId = Number(route.params.id)
  if (brandId) {
    await brandsStore.fetchBrand(brandId)
  }
})
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
