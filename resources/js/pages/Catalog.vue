<template>
  <div class="min-h-screen bg-gradient-to-b from-slate-900 to-slate-800">
    <!-- Header -->
    <div class="sticky top-0 z-50 bg-slate-950 shadow-lg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-white">🛍️ Catálogo de Productos</h1>
        <div class="text-sm text-gray-300">
          <span class="badge badge-primary">{{ filteredProducts.length }} productos</span>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Filters -->
      <div class="mb-8 space-y-4 md:space-y-0 md:flex md:gap-4">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="🔍 Buscar productos..."
          class="flex-1 px-4 py-2 rounded-lg bg-slate-800 text-white placeholder-gray-400 border border-slate-700 focus:border-blue-500 focus:outline-none"
        />
        <select
          v-model="selectedCategory"
          class="px-4 py-2 rounded-lg bg-slate-800 text-white border border-slate-700 focus:border-blue-500 focus:outline-none"
        >
          <option value="">Todas las categorías</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center h-64">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
      </div>

      <!-- Products Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="product in filteredProducts"
          :key="product.id"
          class="bg-slate-800 rounded-lg overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105"
        >
          <!-- Image -->
          <div class="relative h-64 bg-slate-700 overflow-hidden">
            <img
              v-if="product.image_path"
              :src="product.image_path"
              :alt="product.name"
              class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
              <span class="text-6xl">📦</span>
            </div>
            <div v-if="product.stock <= product.min_stock" class="absolute top-2 right-2 badge badge-warning">
              ⚠️ Bajo stock
            </div>
          </div>

          <!-- Content -->
          <div class="p-4">
            <h3 class="text-lg font-bold text-white mb-2">{{ product.name }}</h3>
            <p class="text-sm text-gray-300 mb-3 line-clamp-2">{{ product.description }}</p>

            <!-- Price -->
            <div class="mb-4">
              <span class="text-2xl font-bold text-green-400">S/. {{ parseFloat(product.price).toFixed(2) }}</span>
              <span class="text-xs text-gray-400 ml-2">SKU: {{ product.sku }}</span>
            </div>

            <!-- Stock -->
            <div class="mb-4 text-sm">
              <span v-if="product.stock > 0" class="text-green-400">✓ {{ product.stock }} en stock</span>
              <span v-else class="text-red-400">✗ Agotado</span>
            </div>

            <!-- Actions -->
            <div class="flex gap-2">
              <button
                @click="buyOnWhatsApp(product)"
                :disabled="product.stock <= 0"
                class="flex-1 bg-green-500 hover:bg-green-600 disabled:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg transition-colors"
              >
                💬 WhatsApp
              </button>
              <button
                @click="requestQuote(product)"
                class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition-colors"
              >
                📋 Cotizar
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && filteredProducts.length === 0" class="text-center py-12">
        <p class="text-gray-400 text-xl">No se encontraron productos</p>
      </div>
    </div>

    <!-- Footer with Contact -->
    <div class="bg-slate-950 border-t border-slate-700 mt-12 py-8">
      <div class="max-w-7xl mx-auto px-4 text-center text-gray-400">
        <p>¿Preguntas? Contáctanos por WhatsApp</p>
        <a :href="whatsappLink" target="_blank" class="text-green-400 font-bold hover:underline">
          📞 +57 300 1234567
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const products = ref([]);
const categories = ref([]);
const loading = ref(true);
const searchQuery = ref('');
const selectedCategory = ref('');
const whatsappPhone = ref('573001234567'); // Cambiar con el número real

const filteredProducts = computed(() => {
  if (!Array.isArray(products.value)) {
    return [];
  }
  return products.value.filter(product => {
    const matchesSearch = product.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                         product.description.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesCategory = !selectedCategory.value || product.category_id === parseInt(selectedCategory.value);
    return matchesSearch && matchesCategory;
  });
});

const whatsappLink = computed(() => {
  return `https://wa.me/${whatsappPhone.value}`;
});

const fetchProducts = async () => {
  try {
    const response = await axios.get('public/products');
    products.value = response.data;
  } catch (error) {
    console.error('Error fetching products:', error);
  } finally {
    loading.value = false;
  }
};

const fetchCategories = async () => {
  try {
    const response = await axios.get('public/categories');
    categories.value = response.data.data || response.data;
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

const buyOnWhatsApp = (product) => {
  const message = `Hola, estoy interesado en comprar: *${product.name}* - S/. ${parseFloat(product.price).toFixed(2)}`;
  const url = `https://wa.me/${whatsappPhone.value}?text=${encodeURIComponent(message)}`;
  window.open(url, '_blank');
};

const requestQuote = (product) => {
  const message = `Hola, me gustaría solicitar una cotización para: *${product.name}* - S/. ${parseFloat(product.price).toFixed(2)}`;
  const url = `https://wa.me/${whatsappPhone.value}?text=${encodeURIComponent(message)}`;
  window.open(url, '_blank');
};

onMounted(() => {
  fetchProducts();
  fetchCategories();
});
</script>

<style scoped>
.badge {
  @apply inline-block px-2 py-1 rounded text-xs font-semibold;
}

.badge-primary {
  @apply bg-blue-500 text-white;
}

.badge-warning {
  @apply bg-yellow-500 text-white;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
