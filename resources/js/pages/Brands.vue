<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Marcas</h1>
        <p class="text-gray-600">Explora todas las marcas disponibles en nuestro catálogo</p>
      </div>
    </div>

    <!-- Search -->
    <div class="mb-6">
      <div class="max-w-md">
        <input
          v-model="searchTerm"
          @input="handleSearch"
          type="text"
          placeholder="Buscar marcas..."
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
      <p class="mt-4 text-gray-600">Cargando marcas...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="hasError" class="text-center py-12">
      <div class="text-red-600 mb-4">
        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h3 class="text-lg font-medium">Error al cargar las marcas</h3>
        <p class="text-gray-600">{{ error }}</p>
      </div>
      <button 
        @click="() => loadBrands()" 
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
      >
        Reintentar
      </button>
    </div>

    <!-- Brands Grid -->
    <div v-else-if="brands.length > 0" class="space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <router-link
          v-for="brand in brands"
          :key="brand.id"
          :to="`/brands/${brand.id}`"
          class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-all duration-200 group border border-gray-200"
        >
          <div class="flex items-start justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
              {{ brand.name }}
            </h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
              {{ brand.products_count }} producto{{ brand.products_count !== 1 ? 's' : '' }}
            </span>
          </div>
          
          <p class="text-gray-600 mb-4 line-clamp-3">
            {{ brand.description || 'Sin descripción disponible' }}
          </p>
          
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-500">
              Creada: {{ formatDate(brand.created_at) }}
            </span>
            <span class="text-blue-600 group-hover:text-blue-700 font-medium text-sm">
              Ver productos →
            </span>
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
        <h3 class="text-lg font-medium text-gray-900">No se encontraron marcas</h3>
        <p class="text-gray-600">
          {{ searchTerm ? 'Intenta con otros términos de búsqueda' : 'No hay marcas disponibles en este momento' }}
        </p>
      </div>
      <button 
        v-if="searchTerm"
        @click="clearSearch" 
        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
      >
        Limpiar búsqueda
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useBrandsStore } from '@/stores/brands'

const brandsStore = useBrandsStore()

// State
const searchTerm = ref('')
const currentPage = ref(1)

// Computed
const brands = computed(() => brandsStore.brands)
const isLoading = computed(() => brandsStore.isLoading)
const hasError = computed(() => brandsStore.hasError)
const error = computed(() => brandsStore.error)
const meta = computed(() => brandsStore.meta)

// Methods
const loadBrands = async (page = 1) => {
  currentPage.value = page
  await brandsStore.fetchBrands({
    search: searchTerm.value || undefined,
    per_page: 15
  })
}

const handleSearch = () => {
  // Debounce search
  setTimeout(() => {
    loadBrands(1)
  }, 300)
}

const clearSearch = () => {
  searchTerm.value = ''
  loadBrands(1)
}

const goToPage = (page: number) => {
  if (page >= 1 && meta.value && page <= meta.value.last_page) {
    loadBrands(page)
  }
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Watch for search term changes
watch(searchTerm, () => {
  if (searchTerm.value === '') {
    loadBrands(1)
  }
})

// Lifecycle
onMounted(() => {
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
