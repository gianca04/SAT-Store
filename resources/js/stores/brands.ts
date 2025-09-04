import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { publicApi } from '@/services/api'
import type { Brand, SearchFilters, PaginationMeta } from '@/types'

export const useBrandsStore = defineStore('brands', () => {
  // State
  const brands = ref<Brand[]>([])
  const currentBrand = ref<Brand | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)
  const meta = ref<PaginationMeta | null>(null)

  // Getters
  const brandCount = computed(() => brands.value.length)
  const hasError = computed(() => !!error.value)
  const isLoading = computed(() => loading.value)

  // Actions
  const fetchBrands = async (filters?: SearchFilters) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await publicApi.getBrands(filters)
      brands.value = response.data
      meta.value = response.meta || null
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error al cargar las marcas'
      console.error('Error fetching brands:', err)
    } finally {
      loading.value = false
    }
  }

  const fetchBrand = async (id: number) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await publicApi.getBrand(id)
      currentBrand.value = response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error al cargar la marca'
      console.error('Error fetching brand:', err)
    } finally {
      loading.value = false
    }
  }

  const clearError = () => {
    error.value = null
  }

  const clearCurrentBrand = () => {
    currentBrand.value = null
  }

  // Buscar marca por ID en el store local
  const getBrandById = (id: number) => {
    return brands.value.find(brand => brand.id === id) || null
  }

  return {
    // State
    brands,
    currentBrand,
    loading,
    error,
    meta,
    // Getters
    brandCount,
    hasError,
    isLoading,
    // Actions
    fetchBrands,
    fetchBrand,
    clearError,
    clearCurrentBrand,
    getBrandById
  }
})
