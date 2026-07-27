<template>
  <app-layout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Productos</h1>
          <p class="text-gray-600 mt-1">Gestiona tu catálogo de productos y servicios</p>
        </div>
        <button
          @click="showModal = true"
          class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          ➕ Nuevo Producto
        </button>
      </div>

      <!-- Filters -->
      <div class="flex gap-4">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="🔍 Buscar productos..."
          class="flex-1 px-4 py-2 rounded-lg border border-gray-300 focus:border-blue-500 focus:outline-none"
        />
        <select
          v-model="selectedCategory"
          class="px-4 py-2 rounded-lg border border-gray-300 focus:border-blue-500 focus:outline-none"
        >
          <option value="">Todas las categorías</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>
      </div>

      <!-- Products Table -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="text-left text-sm font-semibold text-gray-700 px-6 py-4">Imagen</th>
                <th class="text-left text-sm font-semibold text-gray-700 px-6 py-4">Producto</th>
                <th class="text-left text-sm font-semibold text-gray-700 px-6 py-4">SKU</th>
                <th class="text-right text-sm font-semibold text-gray-700 px-6 py-4">Precio</th>
                <th class="text-right text-sm font-semibold text-gray-700 px-6 py-4">Stock</th>
                <th class="text-right text-sm font-semibold text-gray-700 px-6 py-4">Margen</th>
                <th class="text-center text-sm font-semibold text-gray-700 px-6 py-4">Estado</th>
                <th class="text-center text-sm font-semibold text-gray-700 px-6 py-4">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="product in filteredProducts" :key="product.id" class="border-b border-gray-100 hover:bg-gray-50">
                <!-- Image -->
                <td class="px-6 py-4">
                  <div v-if="product.image_path" class="w-12 h-12 rounded-lg overflow-hidden">
                    <img :src="product.image_path" :alt="product.name" class="w-full h-full object-cover" />
                  </div>
                  <div v-else class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                </td>

                <!-- Name & Category -->
                <td class="px-6 py-4">
                  <div>
                    <p class="font-medium text-gray-900">{{ product.name }}</p>
                    <p class="text-xs text-gray-600">{{ product.category?.name || 'Sin categoría' }}</p>
                  </div>
                </td>

                <!-- SKU -->
                <td class="px-6 py-4 text-sm text-gray-600">{{ product.sku }}</td>

                <!-- Price -->
                <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">{{ formatSoles(product.price) }}</td>

                <!-- Stock -->
                <td class="px-6 py-4 text-right">
                  <span :class="product.stock < 10 ? 'text-red-600 font-semibold' : 'text-green-600 font-semibold'">
                    {{ product.stock }}
                  </span>
                </td>

                <!-- Margin -->
                <td class="px-6 py-4 text-right text-sm text-gray-600">{{ Math.round((product.price - product.cost) / product.price * 100) }}%</td>

                <!-- Status -->
                <td class="px-6 py-4 text-center">
                  <button
                    @click="toggleActive(product)"
                    :class="['px-3 py-1 rounded-full text-sm font-semibold transition-colors', 
                      product.active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200']"
                  >
                    {{ product.active ? '✓ Activo' : '✗ Inactivo' }}
                  </button>
                </td>

                <!-- Actions -->
                <td class="px-6 py-4 text-center space-x-2">
                  <button @click="editProduct(product)" class="text-blue-600 hover:text-blue-900 font-medium">✏️ Editar</button>
                  <button @click="deleteProduct(product.id)" class="text-red-600 hover:text-red-900 font-medium">🗑️ Eliminar</button>
                </td>
              </tr>
              <tr v-if="!filteredProducts || filteredProducts.length === 0">
                <td colspan="8" class="py-8 text-center text-gray-500">No hay productos registrados</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
          <!-- Modal Header -->
          <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4 text-white flex justify-between items-center">
            <h3 class="text-lg font-semibold">{{ editingProduct.id ? 'Editar Producto' : 'Nuevo Producto' }}</h3>
            <button @click="closeModal" class="text-white hover:text-gray-200">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Modal Content -->
          <div class="p-6 space-y-4">
            <!-- Nombre -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre del Producto</label>
              <input
                v-model="editingProduct.name"
                type="text"
                placeholder="Ej: Laptop Dell XPS 13"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <!-- Descripción -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Descripción</label>
              <textarea
                v-model="editingProduct.description"
                placeholder="Descripción del producto"
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              ></textarea>
            </div>

            <!-- Categoría, SKU, Precio -->
            <div class="grid grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Categoría</label>
                <select
                  v-model="editingProduct.category_id"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Seleccionar categoría</option>
                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">SKU</label>
                <input
                  v-model="editingProduct.sku"
                  type="text"
                  placeholder="Ej: DELL-XPS13-001"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Precio de Venta (S/.)</label>
                <input
                  v-model.number="editingProduct.price"
                  type="number"
                  step="0.01"
                  placeholder="0.00"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
            </div>

            <!-- Costo, Stock, Stock Mínimo -->
            <div class="grid grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Costo (S/.)</label>
                <input
                  v-model.number="editingProduct.cost"
                  type="number"
                  step="0.01"
                  placeholder="0.00"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Stock Disponible</label>
                <input
                  v-model.number="editingProduct.stock"
                  type="number"
                  placeholder="0"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Stock Mínimo</label>
                <input
                  v-model.number="editingProduct.min_stock"
                  type="number"
                  placeholder="0"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
            </div>

            <!-- Image Upload -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Imagen del Producto</label>
              <div v-if="!editingProduct.id" class="text-sm text-yellow-700 bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                ⚠️ Guarda el producto primero, luego podrás subir una imagen
              </div>
              <div v-else class="flex gap-4">
                <div v-if="editingProduct.image_path" class="flex-shrink-0">
                  <img :src="editingProduct.image_path" :alt="editingProduct.name" class="h-24 w-24 rounded-lg object-cover border border-gray-200" />
                </div>
                <div class="flex-1">
                  <input
                    type="file"
                    @change="handleImageUpload"
                    accept="image/*"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg cursor-pointer"
                  />
                  <p class="text-xs text-gray-500 mt-2">JPG, PNG, GIF. Máximo 2MB</p>
                </div>
              </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-6">
              <button
                @click="saveProduct"
                :disabled="savingProduct"
                class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-500 font-semibold transition-colors"
              >
                {{ savingProduct ? '⏳ Guardando...' : '💾 Guardar Producto' }}
              </button>
              <button
                @click="closeModal"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition-colors"
              >
                Cancelar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '../layouts/AppLayout.vue';
import { formatSoles } from '../utils/format.js';

export default {
  name: 'Products',
  components: {
    AppLayout
  },
  data() {
    return {
      products: [],
      categories: [],
      showModal: false,
      searchQuery: '',
      selectedCategory: '',
      savingProduct: false,
      editingProduct: {
        id: null,
        name: '',
        description: '',
        sku: '',
        category_id: '',
        price: 0,
        cost: 0,
        stock: 0,
        min_stock: 0,
        image_path: '',
        active: true,
      }
    };
  },
  computed: {
    filteredProducts() {
      if (!Array.isArray(this.products)) {
        return [];
      }
      return this.products.filter(product => {
        const matchesSearch = product.name.toLowerCase().includes(this.searchQuery.toLowerCase());
        const matchesCategory = !this.selectedCategory || product.category_id === parseInt(this.selectedCategory);
        return matchesSearch && matchesCategory;
      });
    }
  },
  methods: {
    formatSoles,
    async loadProducts() {
      try {
        const response = await window.axios.get('/products');
        this.products = response.data.data || [];
      } catch (error) {
        console.error('Error loading products:', error);
        alert('Error al cargar productos');
      }
    },
    async loadCategories() {
      try {
        const response = await window.axios.get('/categories');
        this.categories = response.data.data || [];
      } catch (error) {
        console.error('Error loading categories:', error);
      }
    },
    editProduct(product) {
      this.editingProduct = { ...product };
      this.showModal = true;
    },
    resetForm() {
      this.editingProduct = {
        id: null,
        name: '',
        description: '',
        sku: '',
        category_id: '',
        price: 0,
        cost: 0,
        stock: 0,
        min_stock: 0,
        image_path: '',
        active: true,
      };
    },
    closeModal() {
      this.showModal = false;
      this.resetForm();
    },
    async saveProduct() {
      try {
        this.savingProduct = true;
        const method = this.editingProduct.id ? 'put' : 'post';
        const url = this.editingProduct.id ? `/products/${this.editingProduct.id}` : '/products';
        
        const response = await window.axios[method](url, this.editingProduct);
        
        if (!this.editingProduct.id) {
          // Para nuevo producto, actualizar con ID del producto creado
          this.editingProduct = response.data.product || response.data;
        }

        await this.loadProducts();
        alert(this.editingProduct.id ? '✓ Producto actualizado' : '✓ Producto creado. Ahora puedes subir una imagen.');
        
        // Solo cerrar modal si es edición, si es nueva permitir subir imagen
        if (method === 'put') {
          this.closeModal();
        }
      } catch (error) {
        console.error('Error saving product:', error);
        alert('Error al guardar producto: ' + (error.response?.data?.message || error.message));
      } finally {
        this.savingProduct = false;
      }
    },
    async handleImageUpload(event) {
      const file = event.target.files[0];
      if (!file) return;

      if (!this.editingProduct.id) {
        alert('Por favor guarda el producto primero');
        return;
      }

      try {
        const formData = new FormData();
        formData.append('image', file);

        const response = await window.axios.post(
          `/products/${this.editingProduct.id}/image`,
          formData,
          {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          }
        );

        this.editingProduct.image_path = response.data.image_path;
        await this.loadProducts();
        alert('✓ Imagen actualizada exitosamente');
      } catch (error) {
        console.error('Error uploading image:', error);
        alert('Error al subir la imagen');
      }
    },
    async toggleActive(product) {
      try {
        await window.axios.put(`/products/${product.id}`, { active: !product.active });
        product.active = !product.active;
      } catch (error) {
        console.error('Error toggling product status:', error);
        alert('Error al cambiar el estado del producto');
      }
    },
    async deleteProduct(id) {
      if (!confirm('¿Está seguro de que desea eliminar este producto?')) return;

      try {
        await window.axios.delete(`/products/${id}`);
        await this.loadProducts();
        alert('✓ Producto eliminado');
      } catch (error) {
        console.error('Error deleting product:', error);
        alert('Error al eliminar producto');
      }
    }
  },
  async mounted() {
    await this.loadProducts();
    await this.loadCategories();
  }
};
</script>

<style scoped>
</style>
