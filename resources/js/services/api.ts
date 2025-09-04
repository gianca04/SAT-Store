import axios from 'axios'
import type { 
  Brand, 
  Product, 
  ProductPhoto, 
  ApiResponse, 
  CreateBrandForm, 
  UpdateBrandForm,
  CreateProductForm,
  UpdateProductForm,
  CreateProductPhotoForm,
  UpdateProductPhotoForm,
  SearchFilters 
} from '@/types'

// Configurar axios
const api = axios.create({
  baseURL: '/api',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  }
})

// Interceptor para manejar errores globalmente
api.interceptors.response.use(
  (response) => response,
  (error) => {
    console.error('API Error:', error.response?.data || error.message)
    return Promise.reject(error)
  }
)

// API Pública
export const publicApi = {
  // Marcas públicas
  async getBrands(filters?: SearchFilters): Promise<ApiResponse<Brand[]>> {
    const params = new URLSearchParams()
    if (filters?.search) params.append('search', filters.search)
    if (filters?.per_page) params.append('per_page', filters.per_page.toString())
    
    const response = await api.get(`/public/brands?${params}`)
    return response.data
  },

  async getBrand(id: number): Promise<ApiResponse<Brand>> {
    const response = await api.get(`/public/brands/${id}`)
    return response.data
  },

  async getBrandProducts(id: number, filters?: SearchFilters): Promise<ApiResponse<Product[]>> {
    const params = new URLSearchParams()
    if (filters?.search) params.append('search', filters.search)
    if (filters?.per_page) params.append('per_page', filters.per_page.toString())
    
    const response = await api.get(`/public/brands/${id}/products?${params}`)
    return response.data
  },

  // Productos públicos
  async getProducts(filters?: SearchFilters): Promise<ApiResponse<Product[]>> {
    const params = new URLSearchParams()
    if (filters?.search) params.append('search', filters.search)
    if (filters?.brand_id) params.append('brand_id', filters.brand_id.toString())
    if (filters?.per_page) params.append('per_page', filters.per_page.toString())
    
    const response = await api.get(`/public/products?${params}`)
    return response.data
  },

  async getProduct(id: number): Promise<ApiResponse<Product>> {
    const response = await api.get(`/public/products/${id}`)
    return response.data
  }
}

// API de Administración
export const adminApi = {
  // Marcas
  async getBrands(filters?: SearchFilters): Promise<ApiResponse<Brand[]>> {
    const params = new URLSearchParams()
    if (filters?.search) params.append('search', filters.search)
    if (filters?.per_page) params.append('per_page', filters.per_page.toString())
    
    const response = await api.get(`/admin/brands?${params}`)
    return response.data
  },

  async getBrand(id: number): Promise<ApiResponse<Brand>> {
    const response = await api.get(`/admin/brands/${id}`)
    return response.data
  },

  async createBrand(data: CreateBrandForm): Promise<ApiResponse<Brand>> {
    const response = await api.post('/admin/brands', data)
    return response.data
  },

  async updateBrand(id: number, data: UpdateBrandForm): Promise<ApiResponse<Brand>> {
    const response = await api.put(`/admin/brands/${id}`, data)
    return response.data
  },

  async deleteBrand(id: number): Promise<{ message: string }> {
    const response = await api.delete(`/admin/brands/${id}`)
    return response.data
  },

  // Productos
  async getProducts(filters?: SearchFilters): Promise<ApiResponse<Product[]>> {
    const params = new URLSearchParams()
    if (filters?.search) params.append('search', filters.search)
    if (filters?.brand_id) params.append('brand_id', filters.brand_id.toString())
    if (filters?.active !== undefined) params.append('active', filters.active.toString())
    if (filters?.per_page) params.append('per_page', filters.per_page.toString())
    
    const response = await api.get(`/admin/products?${params}`)
    return response.data
  },

  async getProduct(id: number): Promise<ApiResponse<Product>> {
    const response = await api.get(`/admin/products/${id}`)
    return response.data
  },

  async createProduct(data: CreateProductForm): Promise<ApiResponse<Product>> {
    const response = await api.post('/admin/products', data)
    return response.data
  },

  async updateProduct(id: number, data: UpdateProductForm): Promise<ApiResponse<Product>> {
    const response = await api.put(`/admin/products/${id}`, data)
    return response.data
  },

  async deleteProduct(id: number): Promise<{ message: string }> {
    const response = await api.delete(`/admin/products/${id}`)
    return response.data
  },

  // Fotos de productos
  async getProductPhotos(filters?: SearchFilters): Promise<ApiResponse<ProductPhoto[]>> {
    const params = new URLSearchParams()
    if (filters?.product_id) params.append('product_id', filters.product_id.toString())
    if (filters?.per_page) params.append('per_page', filters.per_page.toString())
    
    const response = await api.get(`/admin/product-photos?${params}`)
    return response.data
  },

  async getProductPhoto(id: number): Promise<ApiResponse<ProductPhoto>> {
    const response = await api.get(`/admin/product-photos/${id}`)
    return response.data
  },

  async createProductPhoto(data: CreateProductPhotoForm): Promise<ApiResponse<ProductPhoto>> {
    const response = await api.post('/admin/product-photos', data)
    return response.data
  },

  async updateProductPhoto(id: number, data: UpdateProductPhotoForm): Promise<ApiResponse<ProductPhoto>> {
    const response = await api.put(`/admin/product-photos/${id}`, data)
    return response.data
  },

  async deleteProductPhoto(id: number): Promise<{ message: string }> {
    const response = await api.delete(`/admin/product-photos/${id}`)
    return response.data
  },

  // File upload endpoints
  async uploadBrandImage(brandId: number, file: File): Promise<ApiResponse<any>> {
    const formData = new FormData()
    formData.append('image', file)
    
    const response = await api.post(`/admin/brands/${brandId}/upload-image`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      }
    })
    return response.data
  },

  async deleteBrandImage(brandId: number): Promise<{ message: string }> {
    const response = await api.delete(`/admin/brands/${brandId}/delete-image`)
    return response.data
  },

  async uploadProductPhoto(productId: number, file: File, description?: string): Promise<ApiResponse<any>> {
    const formData = new FormData()
    formData.append('image', file)
    if (description) {
      formData.append('description', description)
    }
    
    const response = await api.post(`/admin/products/${productId}/upload-photo`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      }
    })
    return response.data
  },

  async deleteProductPhotoFile(photoId: number): Promise<{ message: string }> {
    const response = await api.delete(`/admin/product-photos/${photoId}/delete-photo`)
    return response.data
  }
}

export default api
