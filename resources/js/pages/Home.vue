<template>
  <Layout>
    <!-- Hero Section -->
    <HeroBanner />

    <!-- Featured Categories -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Shop by Category</h2>
          <p class="text-gray-600 max-w-2xl mx-auto">
            Discover our carefully curated selection of premium products across multiple categories
          </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
          <div
            v-for="category in featuredCategories"
            :key="category.id"
            class="group cursor-pointer"
          >
            <div class="bg-gray-50 rounded-2xl p-6 text-center hover:bg-blue-50 transition-colors duration-300">
              <div class="w-16 h-16 mx-auto mb-4 bg-white rounded-full flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                <i :class="category.icon" class="text-2xl text-blue-600 group-hover:scale-110 transition-transform"></i>
              </div>
              <h3 class="font-semibold text-gray-900 mb-1">{{ category.name }}</h3>
              <p class="text-sm text-gray-500">{{ category.count }} items</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Flash Sale -->
    <section class="py-16 bg-gradient-to-r from-red-600 to-pink-600">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center text-white mb-12">
          <div class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-full text-sm font-medium mb-4">
            <i class="fas fa-bolt text-yellow-400 mr-2"></i>
            Flash Sale - Limited Time!
          </div>
          <h2 class="text-4xl font-bold mb-4">Up to 70% Off</h2>
          <p class="text-red-100 text-lg mb-8">Don't miss out on these incredible deals!</p>
          
          <!-- Countdown Timer -->
          <div class="flex justify-center space-x-4 mb-8">
            <div class="bg-white bg-opacity-20 rounded-lg p-4 backdrop-blur-sm">
              <div class="text-2xl font-bold">{{ countdown.hours }}</div>
              <div class="text-sm">Hours</div>
            </div>
            <div class="bg-white bg-opacity-20 rounded-lg p-4 backdrop-blur-sm">
              <div class="text-2xl font-bold">{{ countdown.minutes }}</div>
              <div class="text-sm">Minutes</div>
            </div>
            <div class="bg-white bg-opacity-20 rounded-lg p-4 backdrop-blur-sm">
              <div class="text-2xl font-bold">{{ countdown.seconds }}</div>
              <div class="text-sm">Seconds</div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <ProductCard
            v-for="product in flashSaleProducts"
            :key="product.id"
            :product="product"
          />
        </div>
      </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-12">
          <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Featured Products</h2>
            <p class="text-gray-600">Hand-picked products just for you</p>
          </div>
          <router-link 
            to="/products"
            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors"
          >
            View All Products
          </router-link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
          <ProductCard
            v-for="product in featuredProducts"
            :key="product.id"
            :product="product"
          />
        </div>
      </div>
    </section>

    <!-- Brands -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Trusted Brands</h2>
          <p class="text-gray-600">We partner with the world's leading brands</p>
        </div>

        <div v-if="isLoading" class="text-center py-8">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p class="mt-2 text-gray-600">Loading brands...</p>
        </div>

        <div v-else class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8">
          <div
            v-for="brand in trustedBrands"
            :key="brand.id"
            class="flex items-center justify-center p-6 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer"
          >
            <img
              :src="brand.image_url || `https://via.placeholder.com/120x60/9CA3AF/FFFFFF?text=${brand.name}`"
              :alt="brand.name"
              class="max-h-12 w-auto opacity-60 hover:opacity-100 transition-opacity"
            >
          </div>
        </div>
      </div>
    </section>

    <!-- Features -->
    <section class="py-16 bg-blue-900 text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold mb-4">Why Choose StoreCraft?</h2>
          <p class="text-blue-100 text-lg">We're committed to providing the best shopping experience</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <div
            v-for="feature in features"
            :key="feature.id"
            class="text-center"
          >
            <div class="w-16 h-16 bg-blue-800 rounded-full flex items-center justify-center mx-auto mb-4">
              <i :class="feature.icon" class="text-2xl text-blue-300"></i>
            </div>
            <h3 class="text-xl font-semibold mb-2">{{ feature.title }}</h3>
            <p class="text-blue-100">{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Newsletter -->
    <section class="py-16 bg-gradient-to-r from-purple-600 to-blue-600">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="text-white">
          <h2 class="text-3xl font-bold mb-4">Stay Updated</h2>
          <p class="text-purple-100 text-lg mb-8">
            Subscribe to our newsletter and get 10% off your first order
          </p>
          
          <div class="flex max-w-md mx-auto">
            <input
              type="email"
              v-model="newsletterEmail"
              placeholder="Enter your email address"
              class="flex-1 px-4 py-3 rounded-l-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500"
            >
            <button
              @click="subscribeNewsletter"
              class="px-6 py-3 bg-purple-700 hover:bg-purple-800 rounded-r-lg font-semibold transition-colors"
            >
              Subscribe
            </button>
          </div>
          
          <p class="text-sm text-purple-200 mt-4">
            Join 50,000+ subscribers. Unsubscribe at any time.
          </p>
        </div>
      </div>
    </section>
  </Layout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import Layout from '../components/layout/Layout.vue'
import HeroBanner from '../components/ecommerce/HeroBanner.vue'
import ProductCard from '../components/ecommerce/ProductCard.vue'
import { useBrandsStore } from '../stores/brands'
import { useProductsStore } from '../stores/products'

const brandsStore = useBrandsStore()
const productsStore = useProductsStore()

const newsletterEmail = ref('')
const countdown = ref({
  hours: 12,
  minutes: 34,
  seconds: 56
})

const featuredCategories = ref([
  { id: 1, name: 'Electronics', icon: 'fas fa-laptop', count: 1250 },
  { id: 2, name: 'Fashion', icon: 'fas fa-tshirt', count: 890 },
  { id: 3, name: 'Sports', icon: 'fas fa-dumbbell', count: 456 },
  { id: 4, name: 'Beauty', icon: 'fas fa-heart', count: 320 },
  { id: 5, name: 'Home', icon: 'fas fa-home', count: 675 },
  { id: 6, name: 'Books', icon: 'fas fa-book', count: 234 }
])

const flashSaleProducts = ref([
  {
    id: 1,
    name: 'Wireless Bluetooth Headphones',
    price: 199,
    discount: 50,
    image_url: 'https://via.placeholder.com/300x300/3B82F6/FFFFFF?text=Headphones',
    rating: 4.8,
    reviews_count: 1250,
    stock: 15,
    is_featured: true,
    brand: { name: 'AudioTech' }
  },
  {
    id: 2,
    name: 'Smart Fitness Watch',
    price: 299,
    discount: 40,
    image_url: 'https://via.placeholder.com/300x300/10B981/FFFFFF?text=Watch',
    rating: 4.6,
    reviews_count: 890,
    stock: 8,
    is_new: true,
    brand: { name: 'FitTech' }
  },
  {
    id: 3,
    name: 'Professional Camera',
    price: 899,
    discount: 30,
    image_url: 'https://via.placeholder.com/300x300/8B5CF6/FFFFFF?text=Camera',
    rating: 4.9,
    reviews_count: 567,
    stock: 5,
    brand: { name: 'PhotoPro' }
  },
  {
    id: 4,
    name: 'Gaming Laptop',
    price: 1299,
    discount: 25,
    image_url: 'https://via.placeholder.com/300x300/EF4444/FFFFFF?text=Laptop',
    rating: 4.7,
    reviews_count: 1100,
    stock: 12,
    brand: { name: 'GameTech' }
  }
])

// Computed
const isLoading = computed(() => brandsStore.isLoading)
const trustedBrands = computed(() => brandsStore.brands.slice(0, 6))
const featuredProducts = computed(() => {
  // Show real products if available, otherwise show sample products
  if (productsStore.products.length > 0) {
    // Convert real products to DisplayProduct format
    return productsStore.products.slice(0, 4).map(product => ({
      id: product.id,
      name: product.name,
      brand: product.brand,
      image_url: product.photos?.[0]?.image_url || 'https://via.placeholder.com/300x300/9CA3AF/FFFFFF?text=No+Image',
      // Use default ecommerce values for visual display
      price: 99,
      rating: 4.5,
      reviews_count: 123,
      stock: 20
    }))
  }
  return [
    {
      id: 5,
      name: 'Organic Cotton T-Shirt',
      price: 49,
      image_url: 'https://via.placeholder.com/300x300/F59E0B/FFFFFF?text=T-Shirt',
      rating: 4.5,
      reviews_count: 234,
      stock: 50,
      is_featured: true,
      brand: { name: 'EcoWear' }
    },
    {
      id: 6,
      name: 'Wireless Mouse',
      price: 79,
      image_url: 'https://via.placeholder.com/300x300/06B6D4/FFFFFF?text=Mouse',
      rating: 4.4,
      reviews_count: 456,
      stock: 30,
      brand: { name: 'TechGear' }
    },
    {
      id: 7,
      name: 'Yoga Mat Premium',
      price: 89,
      image_url: 'https://via.placeholder.com/300x300/84CC16/FFFFFF?text=Yoga+Mat',
      rating: 4.8,
      reviews_count: 123,
      stock: 25,
      is_new: true,
      brand: { name: 'YogaLife' }
    },
    {
      id: 8,
      name: 'Coffee Maker Deluxe',
      price: 199,
      image_url: 'https://via.placeholder.com/300x300/DC2626/FFFFFF?text=Coffee',
      rating: 4.6,
      reviews_count: 789,
      stock: 18,
      brand: { name: 'BrewMaster' }
    }
  ]
})

const features = ref([
  {
    id: 1,
    icon: 'fas fa-shipping-fast',
    title: 'Free Shipping',
    description: 'Free delivery on orders over $100'
  },
  {
    id: 2,
    icon: 'fas fa-undo',
    title: 'Easy Returns',
    description: '30-day hassle-free returns'
  },
  {
    id: 3,
    icon: 'fas fa-shield-alt',
    title: 'Secure Payment',
    description: 'Your payment information is safe'
  },
  {
    id: 4,
    icon: 'fas fa-headset',
    title: '24/7 Support',
    description: 'Round-the-clock customer service'
  }
])

let countdownInterval: number | null = null

const updateCountdown = () => {
  if (countdown.value.seconds > 0) {
    countdown.value.seconds--
  } else if (countdown.value.minutes > 0) {
    countdown.value.minutes--
    countdown.value.seconds = 59
  } else if (countdown.value.hours > 0) {
    countdown.value.hours--
    countdown.value.minutes = 59
    countdown.value.seconds = 59
  }
}

const subscribeNewsletter = () => {
  if (newsletterEmail.value && newsletterEmail.value.includes('@')) {
    console.log('Newsletter subscription:', newsletterEmail.value)
    newsletterEmail.value = ''
    // Show success message
  }
}

onMounted(() => {
  // Load data from stores
  brandsStore.fetchBrands()
  productsStore.fetchProducts()
  
  // Start countdown
  countdownInterval = setInterval(updateCountdown, 1000)
})

onUnmounted(() => {
  if (countdownInterval) {
    clearInterval(countdownInterval)
  }
})
</script>
