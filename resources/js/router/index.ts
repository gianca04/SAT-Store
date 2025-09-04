import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'

// Importar componentes de páginas
import Home from '@/pages/Home.vue'
import Brands from '@/pages/Brands.vue'
import BrandDetail from '@/pages/BrandDetail.vue'
import Products from '@/pages/Products.vue'
import ProductDetail from '@/pages/ProductDetail.vue'
import Admin from '@/pages/Admin.vue'

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'home',
    component: Home,
    meta: {
      title: 'Inicio'
    }
  },
  {
    path: '/brands',
    name: 'brands',
    component: Brands,
    meta: {
      title: 'Marcas'
    }
  },
  {
    path: '/brands/:id',
    name: 'brand-detail',
    component: BrandDetail,
    meta: {
      title: 'Detalle de Marca'
    }
  },
  {
    path: '/products',
    name: 'products',
    component: Products,
    meta: {
      title: 'Productos'
    }
  },
  {
    path: '/products/:id',
    name: 'product-detail',
    component: ProductDetail,
    meta: {
      title: 'Detalle de Producto'
    }
  },
  {
    path: '/admin',
    name: 'admin',
    component: Admin,
    meta: {
      title: 'Administración'
    }
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Actualizar el título de la página
router.beforeEach((to) => {
  document.title = to.meta?.title ? `${to.meta.title} - Catálogo` : 'Catálogo de Productos'
})

export default router
