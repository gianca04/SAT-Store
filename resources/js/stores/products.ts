import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { publicApi } from '@/services/api'
import type { Product, SearchFilters, PaginationMeta } from '@/types'

export const useProductsStore = defineStore('products', () => {
  // State
  const products = ref<Product[]>([])
  const currentProduct = ref<Product | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)
  const meta = ref<PaginationMeta | null>(null)

  // Getters
  const productCount = computed(() => products.value.length)
  const activeProducts = computed(() => products.value.filter(p => p.active))
  const hasError = computed(() => !!error.value)
  const isLoading = computed(() => loading.value)

  // Actions
  const fetchProducts = async (filters?: SearchFilters) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await publicApi.getProducts(filters)
      products.value = response.data
      meta.value = response.meta || null
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error al cargar los productos'
      console.error('Error fetching products:', err)
    } finally {
      loading.value = false
    }
  }

  const fetchProduct = async (id: number) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await publicApi.getProduct(id)
      currentProduct.value = response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error al cargar el producto'
      console.error('Error fetching product:', err)
    } finally {
      loading.value = false
    }
  }

  const fetchProductsByBrand = async (brandId: number, filters?: SearchFilters) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await publicApi.getBrandProducts(brandId, filters)
      products.value = response.data
      meta.value = response.meta || null
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error al cargar los productos de la marca'
      console.error('Error fetching brand products:', err)
    } finally {
      loading.value = false
    }
  }

  const clearError = () => {
    error.value = null
  }

  const clearCurrentProduct = () => {
    currentProduct.value = null
  }

  const clearProducts = () => {
    products.value = []
    meta.value = null
  }

  // Buscar producto por ID en el store local
  const getProductById = (id: number) => {
    return products.value.find(product => product.id === id) || null
  }

  // Filtrar productos por marca
  const getProductsByBrand = (brandId: number) => {
    return products.value.filter(product => product.brand_id === brandId)
  }

  return {
    // State
    products,
    currentProduct,
    loading,
    error,
    meta,
    // Getters
    productCount,
    activeProducts,
    hasError,
    isLoading,
    // Actions
    fetchProducts,
    fetchProduct,
    fetchProductsByBrand,
    clearError,
    clearCurrentProduct,
    clearProducts,
    getProductById,
    getProductsByBrand
  }
})
