<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Productos</h1>
        <p class="text-gray-600">Descubre todos los productos disponibles en nuestro catálogo</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Search -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
          <input
            v-model="searchTerm"
            @input="handleSearch"
            type="text"
            placeholder="Buscar productos..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>

        <!-- Brand Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Marca</label>
          <select
            v-model="selectedBrandId"
            @change="handleFilters"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">Todas las marcas</option>
            <option v-for="brand in availableBrands" :key="brand.id" :value="brand.id">
              {{ brand.name }}
            </option>
          </select>
        </div>

        <!-- Status Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
          <select
            v-model="statusFilter"
            @change="handleFilters"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">Todos</option>
            <option value="active">Solo activos</option>
            <option value="inactive">Solo inactivos</option>
          </select>
        </div>
      </div>

      <!-- Quick filters -->
      <div class="mt-4 flex items-center space-x-4">
        <button
          @click="clearFilters"
          class="text-sm text-gray-600 hover:text-gray-900"
        >
          Limpiar filtros
        </button>
        <span class="text-sm text-gray-500">|</span>
        <span class="text-sm text-gray-600">
          {{ meta?.total || 0 }} producto{{ (meta?.total || 0) !== 1 ? 's' : '' }} encontrado{{ (meta?.total || 0) !== 1 ? 's' : '' }}
        </span>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
      <p class="mt-4 text-gray-600">Cargando productos...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="hasError" class="text-center py-12">
      <div class="text-red-600 mb-4">
        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h3 class="text-lg font-medium">Error al cargar los productos</h3>
        <p class="text-gray-600">{{ error }}</p>
      </div>
      <button 
        @click="() => loadProducts()" 
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
      >
        Reintentar
      </button>
    </div>

    <!-- Products Grid -->
    <div v-else-if="products.length > 0" class="space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <router-link
          v-for="product in products"
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
            
            <div class="flex items-center justify-between mb-3">
              <span class="text-sm font-medium text-blue-600">{{ product.brand?.name }}</span>
              <span class="text-sm text-gray-500">
                {{ product.photos_count || 0 }} foto{{ (product.photos_count || 0) !== 1 ? 's' : '' }}
              </span>
            </div>

            <div class="flex items-center justify-between">
              <span class="text-xs text-gray-500">
                {{ formatDate(product.created_at) }}
              </span>
              <span class="text-blue-600 group-hover:text-blue-700 font-medium text-sm">
                Ver detalles →
              </span>
            </div>
          </div>
        </router-link>
      </div>

      <!-- Pagination -->
      <div v-if="meta && meta.last_page > 1" class="flex justify-center">
        <nav class="flex items-center space-x-2">
          <button
            @click="goToPage(meta.current_page - 1)"
            :disabled="meta.current_page <= 1"
            class="px-3 py-2 text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Anterior
          </button>
          
          <span class="px-4 py-2 text-sm text-gray-700">
            Página {{ meta.current_page }} de {{ meta.last_page }}
          </span>
          
          <button
            @click="goToPage(meta.current_page + 1)"
            :disabled="meta.current_page >= meta.last_page"
            class="px-3 py-2 text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Siguiente
          </button>
        </nav>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12">
      <div class="text-gray-400 mb-4">
        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900">No se encontraron productos</h3>
        <p class="text-gray-600">
          {{ hasActiveFilters ? 'Intenta ajustar los filtros de búsqueda' : 'No hay productos disponibles en este momento' }}
        </p>
      </div>
      <button 
        v-if="hasActiveFilters"
        @click="clearFilters" 
        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
      >
        Limpiar filtros
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useProductsStore } from '@/stores/products'
import { useBrandsStore } from '@/stores/brands'

const productsStore = useProductsStore()
const brandsStore = useBrandsStore()

// State
const searchTerm = ref('')
const selectedBrandId = ref<number | ''>('')
const statusFilter = ref('')
const currentPage = ref(1)

// Computed
const products = computed(() => productsStore.products)
const isLoading = computed(() => productsStore.isLoading)
const hasError = computed(() => productsStore.hasError)
const error = computed(() => productsStore.error)
const meta = computed(() => productsStore.meta)
const availableBrands = computed(() => brandsStore.brands)

const hasActiveFilters = computed(() => {
  return searchTerm.value !== '' || selectedBrandId.value !== '' || statusFilter.value !== ''
})

// Methods
const loadProducts = async (page = 1) => {
  currentPage.value = page
  
  const filters: any = {
    per_page: 15
  }
  
  if (searchTerm.value) filters.search = searchTerm.value
  if (selectedBrandId.value) filters.brand_id = Number(selectedBrandId.value)
  // Note: El API público no tiene filtro de status, solo muestra activos
  
  await productsStore.fetchProducts(filters)
}

const loadBrands = async () => {
  if (brandsStore.brands.length === 0) {
    await brandsStore.fetchBrands({ per_page: 100 }) // Cargar todas las marcas
  }
}

const handleSearch = () => {
  // Debounce search
  setTimeout(() => {
    loadProducts(1)
  }, 300)
}

const handleFilters = () => {
  loadProducts(1)
}

const clearFilters = () => {
  searchTerm.value = ''
  selectedBrandId.value = ''
  statusFilter.value = ''
  loadProducts(1)
}

const goToPage = (page: number) => {
  if (page >= 1 && meta.value && page <= meta.value.last_page) {
    loadProducts(page)
  }
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Watch for filter changes
watch([searchTerm, selectedBrandId, statusFilter], () => {
  if (searchTerm.value === '' && selectedBrandId.value === '' && statusFilter.value === '') {
    loadProducts(1)
  }
})

// Lifecycle
onMounted(() => {
  loadProducts()
  loadBrands()
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
