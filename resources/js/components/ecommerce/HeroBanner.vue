<template>
  <section class="relative overflow-hidden bg-gradient-to-br from-blue-900 via-blue-800 to-purple-800">
    <!-- Background Elements -->
    <div class="absolute inset-0">
      <div class="absolute inset-0 bg-black opacity-20"></div>
      <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-500 rounded-full opacity-20 blur-3xl"></div>
      <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-purple-500 rounded-full opacity-20 blur-3xl"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <!-- Hero Content -->
        <div class="text-white space-y-8">
          <div class="space-y-4">
            <div class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-full text-sm font-medium backdrop-blur-sm">
              <i class="fas fa-fire text-orange-400 mr-2"></i>
              Limited Time Offer - Up to 70% Off
            </div>
            
            <h1 class="text-4xl lg:text-6xl font-bold leading-tight">
              Discover
              <span class="bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent">
                Premium
              </span>
              <br>
              Products
            </h1>
            
            <p class="text-xl text-blue-100 leading-relaxed max-w-lg">
              Shop the latest trends and discover amazing deals on thousands of premium products. Free shipping on orders over $100.
            </p>
          </div>

          <!-- CTA Buttons -->
          <div class="flex flex-col sm:flex-row gap-4">
            <button class="px-8 py-4 bg-white text-blue-900 font-bold rounded-lg hover:bg-gray-100 transition-all duration-200 shadow-lg hover:shadow-xl">
              <i class="fas fa-shopping-bag mr-2"></i>
              Shop Now
            </button>
            <button class="px-8 py-4 border-2 border-white text-white font-bold rounded-lg hover:bg-white hover:text-blue-900 transition-all duration-200">
              <i class="fas fa-play mr-2"></i>
              Watch Demo
            </button>
          </div>

          <!-- Stats -->
          <div class="grid grid-cols-3 gap-6 pt-8">
            <div class="text-center">
              <div class="text-2xl font-bold">10K+</div>
              <div class="text-blue-200 text-sm">Happy Customers</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold">50K+</div>
              <div class="text-blue-200 text-sm">Products</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold">99%</div>
              <div class="text-blue-200 text-sm">Satisfaction</div>
            </div>
          </div>
        </div>

        <!-- Hero Image/Products Showcase -->
        <div class="relative">
          <!-- Main Product Card -->
          <div class="bg-white rounded-2xl shadow-2xl p-6 transform rotate-3 hover:rotate-0 transition-transform duration-500">
            <div class="space-y-4">
              <img
                src="https://via.placeholder.com/300x200/3B82F6/FFFFFF?text=Featured+Product"
                alt="Featured Product"
                class="w-full h-48 object-cover rounded-lg"
              >
              <div class="space-y-2">
                <h3 class="font-bold text-gray-900">Premium Wireless Headphones</h3>
                <div class="flex items-center space-x-2">
                  <span class="text-2xl font-bold text-blue-600">$299</span>
                  <span class="text-gray-500 line-through">$399</span>
                  <span class="bg-red-100 text-red-600 px-2 py-1 rounded text-sm font-semibold">25% OFF</span>
                </div>
                <div class="flex items-center space-x-1">
                  <div class="flex text-yellow-400">
                    <i class="fas fa-star" v-for="star in 5" :key="star"></i>
                  </div>
                  <span class="text-gray-500 text-sm">(4.8) 2.1k reviews</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Floating Elements -->
          <div class="absolute -top-6 -right-6 w-24 h-24 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg animate-bounce">
            <div class="text-center">
              <div class="text-sm font-bold text-gray-900">FREE</div>
              <div class="text-xs text-gray-700">SHIPPING</div>
            </div>
          </div>

          <div class="absolute -bottom-6 -left-6 w-20 h-20 bg-green-500 rounded-full flex items-center justify-center shadow-lg">
            <i class="fas fa-check text-white text-2xl"></i>
          </div>

          <!-- Additional Product Cards -->
          <div class="absolute top-12 -left-12 bg-white rounded-lg shadow-lg p-3 transform -rotate-12 hover:rotate-0 transition-transform duration-300">
            <img
              src="https://via.placeholder.com/80x80/8B5CF6/FFFFFF?text=Tech"
              alt="Tech Product"
              class="w-16 h-16 object-cover rounded"
            >
            <div class="mt-2">
              <div class="text-xs font-semibold">Smart Watch</div>
              <div class="text-xs text-blue-600 font-bold">$199</div>
            </div>
          </div>

          <div class="absolute bottom-12 -right-12 bg-white rounded-lg shadow-lg p-3 transform rotate-12 hover:rotate-0 transition-transform duration-300">
            <img
              src="https://via.placeholder.com/80x80/EF4444/FFFFFF?text=Fashion"
              alt="Fashion Product"
              class="w-16 h-16 object-cover rounded"
            >
            <div class="mt-2">
              <div class="text-xs font-semibold">Designer Bag</div>
              <div class="text-xs text-blue-600 font-bold">$89</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white animate-pulse">
      <div class="flex flex-col items-center space-y-2">
        <span class="text-sm">Explore More</span>
        <i class="fas fa-chevron-down animate-bounce"></i>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
// This component is purely presentational, no logic needed for now
</script>
