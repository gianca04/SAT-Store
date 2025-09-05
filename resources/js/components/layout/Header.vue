<template>
  <header class="bg-white shadow-sm border-b">
    <!-- Top Bar -->
    <div class="bg-gray-900 text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-10 text-sm">
          <div class="flex items-center space-x-6">
            <span class="flex items-center">
              <i class="fas fa-truck mr-2"></i>
              Free shipping on orders over $100
            </span>
            <span class="flex items-center">
              <i class="fas fa-phone mr-2"></i>
              +1 (555) 123-4567
            </span>
          </div>
          <div class="flex items-center space-x-4">
            <a href="#" class="hover:text-gray-300 transition-colors">Help</a>
            <a href="#" class="hover:text-gray-300 transition-colors">Track Order</a>
            <div class="flex items-center space-x-2">
              <i class="fas fa-globe"></i>
              <select class="bg-transparent border-none text-white focus:ring-0 text-sm">
                <option>English</option>
                <option>Español</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-20">
        <!-- Logo -->
        <div class="flex items-center">
          <router-link to="/" class="flex items-center space-x-2">
            <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
              <i class="fas fa-store text-white text-lg"></i>
            </div>
            <span class="text-2xl font-bold text-gray-900">StoreCraft</span>
          </router-link>
        </div>

        <!-- Search Bar -->
        <div class="flex-1 max-w-2xl mx-8">
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="fas fa-search text-gray-400"></i>
            </div>
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Search for products, brands and more..."
              class="block w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
              @keyup.enter="performSearch"
            >
            <button
              @click="performSearch"
              class="absolute inset-y-0 right-0 pr-3 flex items-center text-blue-600 hover:text-blue-700"
            >
              <i class="fas fa-arrow-right"></i>
            </button>
          </div>
        </div>

        <!-- User Actions -->
        <div class="flex items-center space-x-6">
          <!-- Wishlist -->
          <button class="relative p-2 text-gray-600 hover:text-blue-600 transition-colors">
            <i class="fas fa-heart text-xl"></i>
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
              {{ wishlistCount }}
            </span>
          </button>

          <!-- Cart -->
          <button 
            @click="toggleCart"
            class="relative p-2 text-gray-600 hover:text-blue-600 transition-colors"
          >
            <i class="fas fa-shopping-cart text-xl"></i>
            <span class="absolute -top-1 -right-1 bg-blue-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
              {{ cartCount }}
            </span>
          </button>

          <!-- User Menu -->
          <div class="relative">
            <button
              @click="toggleUserMenu"
              class="flex items-center space-x-2 p-2 text-gray-600 hover:text-blue-600 transition-colors"
            >
              <i class="fas fa-user text-xl"></i>
              <span class="text-sm font-medium">Account</span>
              <i class="fas fa-chevron-down text-xs"></i>
            </button>

            <!-- User Dropdown -->
            <div
              v-if="showUserMenu"
              class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border z-50"
            >
              <div class="py-2">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  <i class="fas fa-user mr-3"></i>My Profile
                </a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  <i class="fas fa-box mr-3"></i>My Orders
                </a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  <i class="fas fa-heart mr-3"></i>Wishlist
                </a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  <i class="fas fa-cog mr-3"></i>Settings
                </a>
                <div class="border-t border-gray-100 my-2"></div>
                <router-link 
                  to="/admin"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  <i class="fas fa-tools mr-3"></i>Admin Panel
                </router-link>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  <i class="fas fa-sign-out-alt mr-3"></i>Sign Out
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Navigation Bar -->
    <nav class="bg-gray-50 border-t">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14">
          <!-- Categories -->
          <div class="flex items-center space-x-8">
            <button
              @click="toggleCategories"
              class="flex items-center space-x-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
              <i class="fas fa-bars"></i>
              <span class="font-medium">All Categories</span>
              <i class="fas fa-chevron-down text-sm"></i>
            </button>

            <div class="hidden lg:flex items-center space-x-8">
              <a href="#" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">Home</a>
              <a href="#" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">Electronics</a>
              <a href="#" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">Fashion</a>
              <a href="#" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">Sports</a>
              <a href="#" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">Home & Garden</a>
              <a href="#" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">Books</a>
            </div>
          </div>

          <!-- Special Offers -->
          <div class="hidden md:flex items-center space-x-6 text-sm">
            <a href="#" class="text-red-600 font-semibold hover:text-red-700">
              <i class="fas fa-fire mr-1"></i>Hot Deals
            </a>
            <a href="#" class="text-orange-600 font-semibold hover:text-orange-700">
              <i class="fas fa-bolt mr-1"></i>Flash Sale
            </a>
            <a href="#" class="text-green-600 font-semibold hover:text-green-700">
              <i class="fas fa-leaf mr-1"></i>New Arrivals
            </a>
          </div>
        </div>
      </div>

      <!-- Categories Dropdown -->
      <div
        v-if="showCategories"
        class="absolute left-0 right-0 bg-white shadow-lg border-t z-40"
      >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
            <div v-for="category in categories" :key="category.id" class="group">
              <a href="#" class="block">
                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-blue-50 transition-colors">
                  <i :class="category.icon" class="text-2xl text-gray-600 group-hover:text-blue-600"></i>
                </div>
                <h3 class="font-medium text-gray-900 group-hover:text-blue-600">{{ category.name }}</h3>
                <p class="text-sm text-gray-500">{{ category.count }} items</p>
              </a>
            </div>
          </div>
        </div>
      </div>
    </nav>
  </header>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

// Reactive data
const searchQuery = ref('')
const showUserMenu = ref(false)
const showCategories = ref(false)

// Sample data
const wishlistCount = ref(3)
const cartCount = ref(2)

const categories = ref([
  { id: 1, name: 'Electronics', icon: 'fas fa-laptop', count: 1250 },
  { id: 2, name: 'Fashion', icon: 'fas fa-tshirt', count: 890 },
  { id: 3, name: 'Sports', icon: 'fas fa-dumbbell', count: 456 },
  { id: 4, name: 'Books', icon: 'fas fa-book', count: 2340 },
  { id: 5, name: 'Home & Garden', icon: 'fas fa-home', count: 675 },
  { id: 6, name: 'Beauty', icon: 'fas fa-heart', count: 320 },
  { id: 7, name: 'Automotive', icon: 'fas fa-car', count: 189 },
  { id: 8, name: 'Toys', icon: 'fas fa-gamepad', count: 567 },
  { id: 9, name: 'Grocery', icon: 'fas fa-shopping-basket', count: 789 },
  { id: 10, name: 'Health', icon: 'fas fa-plus-circle', count: 234 },
  { id: 11, name: 'Music', icon: 'fas fa-music', count: 345 },
  { id: 12, name: 'Movies', icon: 'fas fa-film', count: 123 }
])

// Methods
const performSearch = () => {
  if (searchQuery.value.trim()) {
    console.log('Searching for:', searchQuery.value)
    // Implement search functionality
  }
}

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
  showCategories.value = false
}

const toggleCategories = () => {
  showCategories.value = !showCategories.value
  showUserMenu.value = false
}

const toggleCart = () => {
  console.log('Toggle cart')
  // Implement cart toggle
}

// Close dropdowns when clicking outside
document.addEventListener('click', (e) => {
  const target = e.target as HTMLElement
  if (!target.closest('.relative')) {
    showUserMenu.value = false
    showCategories.value = false
  }
})
</script>
