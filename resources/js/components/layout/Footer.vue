<template>
  <footer class="bg-gray-900 text-white">
    <!-- Newsletter Section -->
    <div class="bg-gray-800 py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <h3 class="text-2xl font-bold mb-4">Stay in the Loop</h3>
          <p class="text-gray-300 mb-8 max-w-2xl mx-auto">
            Subscribe to our newsletter for exclusive deals, product updates, and style inspiration delivered straight to your inbox.
          </p>
          <div class="flex max-w-md mx-auto">
            <input
              type="email"
              v-model="newsletterEmail"
              placeholder="Enter your email address"
              class="flex-1 px-4 py-3 bg-white text-gray-900 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <button
              @click="subscribeNewsletter"
              class="px-6 py-3 bg-blue-600 hover:bg-blue-700 rounded-r-lg font-semibold transition-colors"
            >
              Subscribe
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Footer Content -->
    <div class="py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <!-- Company Info -->
          <div class="space-y-6">
            <div class="flex items-center space-x-2">
              <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-store text-white text-lg"></i>
              </div>
              <span class="text-2xl font-bold">StoreCraft</span>
            </div>
            <p class="text-gray-300 text-sm leading-relaxed">
              Your premier destination for quality products at unbeatable prices. We're committed to providing exceptional shopping experiences with fast delivery and outstanding customer service.
            </p>
            <div class="flex space-x-4">
              <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-600 transition-colors">
                <i class="fab fa-facebook-f"></i>
              </a>
              <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-600 transition-colors">
                <i class="fab fa-twitter"></i>
              </a>
              <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-600 transition-colors">
                <i class="fab fa-instagram"></i>
              </a>
              <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-blue-600 transition-colors">
                <i class="fab fa-youtube"></i>
              </a>
            </div>
          </div>

          <!-- Quick Links -->
          <div>
            <h4 class="text-lg font-semibold mb-6">Quick Links</h4>
            <ul class="space-y-3">
              <li><a href="#" class="text-gray-300 hover:text-white transition-colors">About Us</a></li>
              <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Contact Us</a></li>
              <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Careers</a></li>
              <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Press & Media</a></li>
              <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Affiliate Program</a></li>
              <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Bulk Orders</a></li>
            </ul>
          </div>

          <!-- Customer Service -->
          <div>
            <h4 class="text-lg font-semibold mb-6">Customer Service</h4>
            <ul class="space-y-3">
              <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Help Center</a></li>
              <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Track Your Order</a></li>
              <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Returns & Exchanges</a></li>
              <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Shipping Information</a></li>
              <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Size Guide</a></li>
              <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Gift Cards</a></li>
            </ul>
          </div>

          <!-- Contact & Support -->
          <div>
            <h4 class="text-lg font-semibold mb-6">Get in Touch</h4>
            <div class="space-y-4">
              <div class="flex items-center space-x-3">
                <i class="fas fa-map-marker-alt text-blue-400"></i>
                <span class="text-gray-300 text-sm">123 Commerce St, Business District, NY 10001</span>
              </div>
              <div class="flex items-center space-x-3">
                <i class="fas fa-phone text-blue-400"></i>
                <span class="text-gray-300 text-sm">+1 (555) 123-4567</span>
              </div>
              <div class="flex items-center space-x-3">
                <i class="fas fa-envelope text-blue-400"></i>
                <span class="text-gray-300 text-sm">support@storecraft.com</span>
              </div>
              <div class="flex items-center space-x-3">
                <i class="fas fa-clock text-blue-400"></i>
                <span class="text-gray-300 text-sm">Mon-Fri: 9AM-8PM EST</span>
              </div>
            </div>

            <!-- Trust Badges -->
            <div class="mt-6">
              <h5 class="text-sm font-semibold mb-3">Secure Shopping</h5>
              <div class="flex space-x-2">
                <div class="bg-gray-800 px-3 py-2 rounded text-xs">
                  <i class="fas fa-shield-alt text-green-400 mr-1"></i>
                  SSL Secured
                </div>
                <div class="bg-gray-800 px-3 py-2 rounded text-xs">
                  <i class="fas fa-credit-card text-blue-400 mr-1"></i>
                  Safe Payment
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Methods & Bottom Bar -->
    <div class="border-t border-gray-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row justify-between items-center space-y-4 lg:space-y-0">
          <!-- Payment Methods -->
          <div class="flex items-center space-x-6">
            <span class="text-sm text-gray-400">We Accept:</span>
            <div class="flex space-x-3">
              <div class="w-12 h-8 bg-white rounded flex items-center justify-center">
                <i class="fab fa-cc-visa text-blue-600 text-lg"></i>
              </div>
              <div class="w-12 h-8 bg-white rounded flex items-center justify-center">
                <i class="fab fa-cc-mastercard text-red-500 text-lg"></i>
              </div>
              <div class="w-12 h-8 bg-white rounded flex items-center justify-center">
                <i class="fab fa-cc-amex text-blue-400 text-lg"></i>
              </div>
              <div class="w-12 h-8 bg-white rounded flex items-center justify-center">
                <i class="fab fa-cc-paypal text-blue-600 text-lg"></i>
              </div>
              <div class="w-12 h-8 bg-white rounded flex items-center justify-center">
                <i class="fab fa-apple-pay text-black text-lg"></i>
              </div>
              <div class="w-12 h-8 bg-white rounded flex items-center justify-center">
                <i class="fab fa-google-pay text-blue-500 text-lg"></i>
              </div>
            </div>
          </div>

          <!-- App Download -->
          <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-400">Download App:</span>
            <a href="#" class="block">
              <img src="https://via.placeholder.com/120x40/000000/FFFFFF?text=App+Store" alt="Download on App Store" class="h-10 rounded">
            </a>
            <a href="#" class="block">
              <img src="https://via.placeholder.com/120x40/000000/FFFFFF?text=Google+Play" alt="Get it on Google Play" class="h-10 rounded">
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Copyright -->
    <div class="bg-gray-800 py-4">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center space-y-2 md:space-y-0">
          <p class="text-sm text-gray-400">
            © {{ currentYear }} StoreCraft. All rights reserved.
          </p>
          <div class="flex space-x-6 text-sm text-gray-400">
            <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
            <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
            <a href="#" class="hover:text-white transition-colors">Cookie Policy</a>
            <a href="#" class="hover:text-white transition-colors">Accessibility</a>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

const newsletterEmail = ref('')
const currentYear = computed(() => new Date().getFullYear())

const subscribeNewsletter = () => {
  if (newsletterEmail.value && newsletterEmail.value.includes('@')) {
    console.log('Subscribing email:', newsletterEmail.value)
    // Implement newsletter subscription
    newsletterEmail.value = ''
    // Show success message
  }
}
</script>
