import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { adminApi } from '@/services/api'
import type { 
  Brand, 
  Product, 
  ProductPhoto, 
  SearchFilters, 
  PaginationMeta,
  CreateBrandForm,
  UpdateBrandForm,
  CreateProductForm,
  UpdateProductForm,
  CreateProductPhotoForm,
  UpdateProductPhotoForm
} from '@/types'

export const useAdminStore = defineStore('admin', () => {
  // State
  const brands = ref<Brand[]>([])
  const products = ref<Product[]>([])
  const productPhotos = ref<ProductPhoto[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)
  const meta = ref<PaginationMeta | null>(null)

  // Getters
  const hasError = computed(() => !!error.value)
  const isLoading = computed(() => loading.value)

  // Actions - Brands
  const fetchBrands = async (filters?: SearchFilters) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await adminApi.getBrands(filters)
      brands.value = response.data
      meta.value = response.meta || null
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error al cargar las marcas'
      console.error('Error fetching admin brands:', err)
    } finally {
      loading.value = false
    }
  }

  const createBrand = async (data: CreateBrandForm) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await adminApi.createBrand(data)
      brands.value.push(response.data)
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error al crear la marca'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateBrand = async (id: number, data: UpdateBrandForm) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await adminApi.updateBrand(id, data)
      const index = brands.value.findIndex(b => b.id === id)
      if (index !== -1) {
        brands.value[index] = response.data
      }
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error al actualizar la marca'
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteBrand = async (id: number) => {
    loading.value = true
    error.value = null
    
    try {
      await adminApi.deleteBrand(id)
      brands.value = brands.value.filter(b => b.id !== id)
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error al eliminar la marca'
      throw err
    } finally {
      loading.value = false
    }
  }

  // Actions - Products
  const fetchProducts = async (filters?: SearchFilters) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await adminApi.getProducts(filters)
      products.value = response.data
      meta.value = response.meta || null
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error al cargar los productos'
      console.error('Error fetching admin products:', err)
    } finally {
      loading.value = false
    }
  }

  const createProduct = async (data: CreateProductForm) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await adminApi.createProduct(data)
      products.value.push(response.data)
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error al crear el producto'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateProduct = async (id: number, data: UpdateProductForm) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await adminApi.updateProduct(id, data)
      const index = products.value.findIndex(p => p.id === id)
      if (index !== -1) {
        products.value[index] = response.data
      }
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error al actualizar el producto'
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteProduct = async (id: number) => {
    loading.value = true
    error.value = null
    
    try {
      await adminApi.deleteProduct(id)
      products.value = products.value.filter(p => p.id !== id)
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error al eliminar el producto'
      throw err
    } finally {
      loading.value = false
    }
  }

  // Actions - Product Photos
  const fetchProductPhotos = async (filters?: SearchFilters) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await adminApi.getProductPhotos(filters)
      productPhotos.value = response.data
      meta.value = response.meta || null
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error al cargar las fotos'
      console.error('Error fetching product photos:', err)
    } finally {
      loading.value = false
    }
  }

  const createProductPhoto = async (data: CreateProductPhotoForm) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await adminApi.createProductPhoto(data)
      productPhotos.value.push(response.data)
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error al crear la foto'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateProductPhoto = async (id: number, data: UpdateProductPhotoForm) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await adminApi.updateProductPhoto(id, data)
      const index = productPhotos.value.findIndex(p => p.id === id)
      if (index !== -1) {
        productPhotos.value[index] = response.data
      }
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error al actualizar la foto'
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteProductPhoto = async (id: number) => {
    loading.value = true
    error.value = null
    
    try {
      await adminApi.deleteProductPhoto(id)
      productPhotos.value = productPhotos.value.filter(p => p.id !== id)
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Error al eliminar la foto'
      throw err
    } finally {
      loading.value = false
    }
  }

  const clearError = () => {
    error.value = null
  }

  return {
    // State
    brands,
    products,
    productPhotos,
    loading,
    error,
    meta,
    // Getters
    hasError,
    isLoading,
    // Brand Actions
    fetchBrands,
    createBrand,
    updateBrand,
    deleteBrand,
    // Product Actions
    fetchProducts,
    createProduct,
    updateProduct,
    deleteProduct,
    // Product Photo Actions
    fetchProductPhotos,
    createProductPhoto,
    updateProductPhoto,
    deleteProductPhoto,
    // Utility Actions
    clearError
  }
})
