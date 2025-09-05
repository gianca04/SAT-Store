<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Productos</h1>
      <p class="text-gray-600 dark:text-gray-400">Descubre todos los productos disponibles en nuestro catálogo</p>
    </div>

    <!-- Filters Card -->
    <div class="max-w-full p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Search Input -->
        <div>
          <label for="search-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Buscar</label>
          <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
              <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
              </svg>
            </div>
            <input 
              id="search-input"
              v-model="searchTerm"
              @input="handleSearch"
              type="search" 
              class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
              placeholder="Buscar productos..."
            />
          </div>
        </div>

        <!-- Brand Filter -->
        <div>
          <label for="brand-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Marca</label>
          <select 
            id="brand-select"
            v-model="selectedBrandId"
            @change="handleFilters"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
          >
            <option value="">Todas las marcas</option>
            <option v-for="brand in availableBrands" :key="brand.id" :value="brand.id">
              {{ brand.name }}
            </option>
          </select>
        </div>

        <!-- Status Filter -->
        <div>
          <label for="status-select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado</label>
          <select 
            id="status-select"
            v-model="statusFilter"
            @change="handleFilters"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
          >
            <option value="">Todos</option>
            <option value="active">Solo activos</option>
            <option value="inactive">Solo inactivos</option>
          </select>
        </div>
      </div>

      <!-- Quick filters -->
      <div class="mt-4 flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <button
            @click="clearFilters"
            type="button"
            class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
          >
            Limpiar filtros
          </button>
        </div>
        <span class="text-sm text-gray-600 dark:text-gray-400">
          {{ meta?.total || 0 }} producto{{ (meta?.total || 0) !== 1 ? 's' : '' }} encontrado{{ (meta?.total || 0) !== 1 ? 's' : '' }}
        </span>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="flex items-center justify-center py-12">
      <div role="status">
        <svg aria-hidden="true" class="w-12 h-12 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
          <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
        </svg>
        <span class="sr-only">Cargando...</span>
      </div>
      <p class="ml-4 text-gray-600 dark:text-gray-400">Cargando productos...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="hasError" class="flex flex-col items-center justify-center py-12">
      <div class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
        </svg>
        <div>
          <span class="font-medium">Error al cargar los productos:</span> {{ error }}
        </div>
      </div>
      <button 
        @click="() => loadProducts()" 
        type="button"
        class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
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
          class="block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 transition-all duration-200"
        >
          <div class="flex items-center justify-between mb-3">
            <h5 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">
              {{ product.name }}
            </h5>
            <span 
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
              :class="product.active 
                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' 
                : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'"
            >
              {{ product.active ? 'Activo' : 'Inactivo' }}
            </span>
          </div>
          
          <p class="mb-3 font-normal text-gray-700 dark:text-gray-400 line-clamp-3">
            {{ product.description || 'Sin descripción disponible' }}
          </p>
          
          <div class="flex items-center justify-between mb-3">
            <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-600 hover:text-white focus:ring-4 focus:outline-none focus:ring-blue-300 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
              {{ product.brand?.name }}
            </span>
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
              <svg class="w-4 h-4 me-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 14">
                <path d="M10 0C4.612 0 0 5.336 0 7c0 1.742 3.546 7 10 7 6.454 0 10-5.258 10-7 0-1.664-4.612-7-10-7Zm0 10a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/>
              </svg>
              {{ product.photos_count || 0 }} foto{{ (product.photos_count || 0) !== 1 ? 's' : '' }}
            </div>
          </div>

          <div class="flex items-center justify-between">
            <span class="text-xs text-gray-500 dark:text-gray-400">
              {{ formatDate(product.created_at) }}
            </span>
            <span class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline">
              Ver detalles
              <svg class="w-4 h-4 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
              </svg>
            </span>
          </div>
        </router-link>
      </div>

      <!-- Pagination -->
      <div v-if="meta && meta.last_page > 1" class="flex justify-center">
        <nav aria-label="Page navigation">
          <ul class="inline-flex -space-x-px rtl:space-x-reverse text-sm h-8">
            <li>
              <button
                @click="goToPage(meta.current_page - 1)"
                :disabled="meta.current_page <= 1"
                class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Anterior
              </button>
            </li>
            <li>
              <span class="flex items-center justify-center px-3 h-8 text-gray-500 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400">
                Página {{ meta.current_page }} de {{ meta.last_page }}
              </span>
            </li>
            <li>
              <button
                @click="goToPage(meta.current_page + 1)"
                :disabled="meta.current_page >= meta.last_page"
                class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Siguiente
              </button>
            </li>
          </ul>
        </nav>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12">
      <div class="flex flex-col items-center justify-center p-4">
        <div class="flex items-center justify-center w-16 h-16 mb-4 bg-gray-100 rounded-lg dark:bg-gray-800">
          <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4 1 8l4 4m10-8 4 4-4 4M11 1 9 15"/>
          </svg>
        </div>
        <h3 class="mb-2 text-lg font-medium text-gray-900 dark:text-white">No se encontraron productos</h3>
        <p class="mb-4 text-gray-600 dark:text-gray-400">
          {{ hasActiveFilters ? 'Intenta ajustar los filtros de búsqueda' : 'No hay productos disponibles en este momento' }}
        </p>
        <button 
          v-if="hasActiveFilters"
          @click="clearFilters" 
          type="button"
          class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
        >
          Limpiar filtros
        </button>
      </div>
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
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
