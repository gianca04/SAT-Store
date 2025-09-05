<template>
  <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow duration-300 group">
    <!-- Product Image -->
    <div class="relative overflow-hidden rounded-t-lg">
      <img
        :src="product.image_url || 'https://via.placeholder.com/300x300/F3F4F6/9CA3AF?text=No+Image'"
        :alt="product.name"
        class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-300"
      >
      
      <!-- Badges -->
      <div class="absolute top-3 left-3 flex flex-col space-y-2">
        <span v-if="product.discount" class="bg-red-500 text-white px-2 py-1 rounded text-xs font-semibold">
          -{{ product.discount }}%
        </span>
        <span v-if="product.is_new" class="bg-green-500 text-white px-2 py-1 rounded text-xs font-semibold">
          New
        </span>
        <span v-if="product.is_featured" class="bg-blue-500 text-white px-2 py-1 rounded text-xs font-semibold">
          Featured
        </span>
      </div>

      <!-- Quick Actions -->
      <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
        <div class="flex flex-col space-y-2">
          <button
            @click="toggleWishlist"
            :class="[
              'w-8 h-8 rounded-full flex items-center justify-center transition-colors',
              isInWishlist ? 'bg-red-500 text-white' : 'bg-white text-gray-600 hover:bg-red-50 hover:text-red-500'
            ]"
          >
            <i :class="isInWishlist ? 'fas fa-heart' : 'far fa-heart'"></i>
          </button>
          
          <button
            @click="quickView"
            class="w-8 h-8 bg-white text-gray-600 hover:bg-blue-50 hover:text-blue-500 rounded-full flex items-center justify-center transition-colors"
          >
            <i class="fas fa-eye"></i>
          </button>
          
          <button
            @click="compare"
            class="w-8 h-8 bg-white text-gray-600 hover:bg-green-50 hover:text-green-500 rounded-full flex items-center justify-center transition-colors"
          >
            <i class="fas fa-balance-scale"></i>
          </button>
        </div>
      </div>

      <!-- Out of Stock Overlay -->
      <div v-if="productStock === 0" class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <span class="bg-white text-gray-900 px-4 py-2 rounded-lg font-semibold">Out of Stock</span>
      </div>
    </div>

    <!-- Product Info -->
    <div class="p-4">
      <!-- Brand -->
      <div class="flex items-center justify-between mb-2">
        <span class="text-sm text-gray-500 font-medium">{{ product.brand?.name || 'No Brand' }}</span>
        <div class="flex items-center space-x-1">
          <div class="flex">
            <i
              v-for="star in 5"
              :key="star"
              :class="[
                'w-3 h-3',
                star <= productRating ? 'text-yellow-400 fas fa-star' : 'text-gray-300 far fa-star'
              ]"
            ></i>
          </div>
          <span class="text-xs text-gray-500">({{ productReviews }})</span>
        </div>
      </div>

      <!-- Product Name -->
      <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2 hover:text-blue-600 transition-colors cursor-pointer">
        {{ product.name }}
      </h3>

      <!-- Price -->
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-2">
          <span class="text-lg font-bold text-gray-900">
            ${{ discountedPrice.toFixed(2) }}
          </span>
          <span v-if="product.discount" class="text-sm text-gray-500 line-through">
            ${{ productPrice }}
          </span>
        </div>
        <span v-if="productStock && productStock <= 5" class="text-xs text-orange-600 font-medium">
          Only {{ productStock }} left!
        </span>
      </div>

      <!-- Add to Cart Button -->
      <button
        @click="addToCart"
        :disabled="productStock === 0"
        :class="[
          'w-full py-2.5 px-4 rounded-lg font-semibold transition-all duration-200',
          productStock === 0
            ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
            : 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm hover:shadow-md'
        ]"
      >
        <i class="fas fa-shopping-cart mr-2"></i>
        {{ productStock === 0 ? 'Out of Stock' : 'Add to Cart' }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import type { DisplayProduct } from '../../types'

interface Props {
  product: DisplayProduct
}

const props = defineProps<Props>()

const isInWishlist = ref(false)

const discountedPrice = computed(() => {
  const basePrice = props.product.price || 99 // Default price if not set
  if (props.product.discount) {
    return basePrice * (1 - props.product.discount / 100)
  }
  return basePrice
})

const productRating = computed(() => props.product.rating || 4.5)
const productReviews = computed(() => props.product.reviews_count || 0)
const productStock = computed(() => props.product.stock || 10)
const productPrice = computed(() => props.product.price || 99)

const toggleWishlist = () => {
  isInWishlist.value = !isInWishlist.value
  console.log('Toggle wishlist for product:', props.product.id)
  // Implement wishlist functionality
}

const addToCart = () => {
  if (productStock.value === 0) return
  console.log('Add to cart:', props.product.id)
  // Implement add to cart functionality
}

const quickView = () => {
  console.log('Quick view:', props.product.id)
  // Implement quick view functionality
}

const compare = () => {
  console.log('Compare:', props.product.id)
  // Implement compare functionality
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
