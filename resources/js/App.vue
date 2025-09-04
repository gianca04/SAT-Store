<template>
  <div id="app">
    <header class="bg-white shadow-sm border-b">
      <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <router-link to="/" class="text-xl font-bold text-gray-900">
              📱 Catálogo de Productos
            </router-link>
          </div>
          <div class="flex items-center space-x-4">
            <router-link
              to="/"
              class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
              :class="{ 'text-blue-600 font-semibold': route.name === 'home' }"
            >
              Inicio
            </router-link>
            <router-link
              to="/brands"
              class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
              :class="{ 'text-blue-600 font-semibold': route.name === 'brands' }"
            >
              Marcas
            </router-link>
            <router-link
              to="/products"
              class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
              :class="{ 'text-blue-600 font-semibold': route.name === 'products' }"
            >
              Productos
            </router-link>
            <router-link
              to="/admin"
              class="bg-blue-600 text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-700"
            >
              Admin
            </router-link>
          </div>
        </div>
      </nav>
    </header>

    <main class="min-h-screen bg-gray-50">
      <router-view />
    </main>

    <footer class="bg-white border-t">
      <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <p class="text-center text-gray-500 text-sm">
          © 2025 Catálogo de Productos. API desarrollado con Laravel + Vue 3 + TypeScript
        </p>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { useRoute } from 'vue-router'

const route = useRoute()
</script>

<style>
#app {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
</style>
