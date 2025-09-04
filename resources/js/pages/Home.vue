<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Hero Section -->
    <div class="text-center mb-12">
      <h1 class="text-4xl font-bold text-gray-900 mb-4">
        Catálogo de Productos
      </h1>
      <p class="text-xl text-gray-600 max-w-2xl mx-auto">
        Descubre nuestra amplia selección de productos de las mejores marcas. 
        Tecnología de vanguardia al alcance de tus manos.
      </p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
      <div class="bg-white rounded-lg shadow-md p-6 text-center">
        <div class="text-3xl font-bold text-blue-600 mb-2">{{ brandCount }}</div>
        <div class="text-gray-600">Marcas Disponibles</div>
      </div>
      <div class="bg-white rounded-lg shadow-md p-6 text-center">
        <div class="text-3xl font-bold text-green-600 mb-2">{{ productCount }}</div>
        <div class="text-gray-600">Productos Activos</div>
      </div>
      <div class="bg-white rounded-lg shadow-md p-6 text-center">
        <div class="text-3xl font-bold text-purple-600 mb-2">API REST</div>
        <div class="text-gray-600">Tecnología Moderna</div>
      </div>
    </div>

    <!-- Featured Brands -->
    <div class="mb-12">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">Marcas Destacadas</h2>
      <div v-if="isLoading" class="text-center py-8">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-2 text-gray-600">Cargando marcas...</p>
      </div>
      <div v-else-if="hasError" class="text-center py-8">
        <p class="text-red-600">{{ error }}</p>
        <button 
          @click="loadBrands" 
          class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          Reintentar
        </button>
      </div>
      <div v-else class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
        <router-link
          v-for="brand in brands"
          :key="brand.id"
          :to="`/brands/${brand.id}`"
          class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200 text-center group"
        >
          <div class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-blue-600">
            {{ brand.name }}
          </div>
          <div class="text-sm text-gray-600 mb-3">
            {{ brand.description?.substring(0, 80) }}{{ brand.description && brand.description.length > 80 ? '...' : '' }}
          </div>
          <div class="text-xs text-blue-600 font-medium">
            {{ brand.products_count }} producto{{ brand.products_count !== 1 ? 's' : '' }}
          </div>
        </router-link>
      </div>
    </div>

    <!-- Featured Products -->
    <div class="mb-12">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">Productos Populares</h2>
      <div v-if="productsLoading" class="text-center py-8">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mx-auto"></div>
        <p class="mt-2 text-gray-600">Cargando productos...</p>
      </div>
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <router-link
          v-for="product in featuredProducts"
          :key="product.id"
          :to="`/products/${product.id}`"
          class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200 group"
        >
          <div class="p-6">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-lg font-semibold text-gray-900 group-hover:text-green-600">
                {{ product.name }}
              </h3>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                Activo
              </span>
            </div>
            <p class="text-gray-600 text-sm mb-3">
              {{ product.description?.substring(0, 100) }}{{ product.description && product.description.length > 100 ? '...' : '' }}
            </p>
            <div class="flex items-center justify-between">
              <span class="text-sm text-blue-600 font-medium">{{ product.brand?.name }}</span>
              <span class="text-xs text-gray-500">{{ product.photos_count }} foto{{ product.photos_count !== 1 ? 's' : '' }}</span>
            </div>
          </div>
        </router-link>
      </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-8 text-center text-white">
      <h2 class="text-2xl font-bold mb-4">¿Listo para explorar?</h2>
      <p class="text-lg mb-6 opacity-90">
        Navega por nuestro catálogo completo y descubre todos los productos disponibles
      </p>
      <div class="space-x-4">
        <router-link
          to="/brands"
          class="inline-block bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors"
        >
          Ver Marcas
        </router-link>
        <router-link
          to="/products"
          class="inline-block bg-transparent border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors"
        >
          Ver Productos
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useBrandsStore } from '@/stores/brands'
import { useProductsStore } from '@/stores/products'

const brandsStore = useBrandsStore()
const productsStore = useProductsStore()

// State
const productsLoading = ref(false)

// Computed
const brands = computed(() => brandsStore.brands.slice(0, 5)) // Solo mostrar 5 marcas
const isLoading = computed(() => brandsStore.isLoading)
const hasError = computed(() => brandsStore.hasError)
const error = computed(() => brandsStore.error)
const brandCount = computed(() => brandsStore.brandCount)

const products = computed(() => productsStore.products)
const featuredProducts = computed(() => products.value.slice(0, 6)) // Solo mostrar 6 productos
const productCount = computed(() => productsStore.productCount)

// Methods
const loadBrands = async () => {
  await brandsStore.fetchBrands({ per_page: 10 })
}

const loadProducts = async () => {
  productsLoading.value = true
  try {
    await productsStore.fetchProducts({ per_page: 10 })
  } finally {
    productsLoading.value = false
  }
}

// Lifecycle
onMounted(() => {
  loadBrands()
  loadProducts()
})
</script>
