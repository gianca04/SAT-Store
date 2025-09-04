// Tipos para las entidades del API
export interface Brand {
  id: number
  name: string
  description: string | null
  foto_path: string | null
  image_url: string | null
  created_at: string
  updated_at: string
  products_count?: number
  products?: Product[]
}

export interface Product {
  id: number
  brand_id: number
  name: string
  description: string | null
  active: boolean
  created_at: string
  updated_at: string
  photos_count?: number
  brand?: Brand
  photos?: ProductPhoto[]
}

export interface ProductPhoto {
  id: number
  product_id: number
  path: string | null
  image_url: string | null
  description: string | null
  created_at: string
  updated_at: string
  product?: Product
}

// Tipos para las respuestas del API
export interface ApiResponse<T> {
  data: T
  meta?: PaginationMeta
}

export interface PaginationMeta {
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface ApiError {
  message: string
  errors?: Record<string, string[]>
}

// Tipos para formularios
export interface CreateBrandForm {
  name: string
  description?: string
  foto_path?: string
}

export interface UpdateBrandForm extends CreateBrandForm {}

export interface CreateProductForm {
  brand_id: number
  name: string
  description?: string
  active: boolean
}

export interface UpdateProductForm extends CreateProductForm {}

export interface CreateProductPhotoForm {
  product_id: number
  description?: string
}

export interface UpdateProductPhotoForm extends CreateProductPhotoForm {}

// Tipos para filtros de búsqueda
export interface SearchFilters {
  search?: string
  per_page?: number
  brand_id?: number
  active?: boolean
  product_id?: number
}
