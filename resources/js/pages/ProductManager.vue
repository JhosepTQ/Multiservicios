<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 to-slate-800">
    <div class="max-w-7xl mx-auto p-6">
      <!-- Header -->
      <div class="mb-8">
        <h2 class="text-3xl font-bold text-white mb-2">Gestionar Productos</h2>
        <button
          @click="() => { editingProduct = { id: null, name: '', description: '', category_id: '', price: 0, cost: 0, stock: 0, min_stock: 0, sku: '', image_path: '', active: true }; showModal = true }"
          class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
        >
          ➕ Nuevo Producto
        </button>
      </div>

      <!-- Filters -->
      <div class="mb-8 bg-slate-800 p-4 rounded-lg flex gap-4">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="🔍 Buscar productos..."
          class="flex-1 px-4 py-2 rounded-lg bg-slate-700 text-white border border-slate-600 focus:border-blue-500 focus:outline-none"
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

      <!-- Products Table -->
      <div class="bg-slate-800 rounded-lg overflow-hidden">
        <table class="w-full text-white text-sm">
          <thead class="bg-slate-900">
            <tr>
              <th class="px-6 py-3 text-left">Imagen</th>
              <th class="px-6 py-3 text-left">Nombre</th>
              <th class="px-6 py-3 text-left">Categoría</th>
              <th class="px-6 py-3 text-left">Precio</th>
              <th class="px-6 py-3 text-left">Stock</th>
              <th class="px-6 py-3 text-center">Activo</th>
              <th class="px-6 py-3 text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="product in filteredProducts"
              :key="product.id"
              class="border-t border-slate-700 hover:bg-slate-700 transition-colors"
            >
              <td class="px-6 py-4">
                <img
                  v-if="product.image_path"
                  :src="product.image_path"
                  :alt="product.name"
                  class="h-12 w-12 rounded object-cover"
                />
                <div v-else class="h-12 w-12 rounded bg-slate-600 flex items-center justify-center text-xs">
                  No img
                </div>
              </td>
              <td class="px-6 py-4">{{ product.name }}</td>
              <td class="px-6 py-4">{{ product.category?.name || 'N/A' }}</td>
              <td class="px-6 py-4">S/. {{ product.price }}</td>
              <td class="px-6 py-4">{{ product.stock }}</td>
              <td class="px-6 py-4 text-center">
                <button
                  @click="toggleActive(product)"
                  :class="product.active ? 'bg-green-600' : 'bg-gray-600'"
                  class="px-3 py-1 rounded text-xs font-semibold"
                >
                  {{ product.active ? '✓' : '✗' }}
                </button>
              </td>
              <td class="px-6 py-4 text-center space-x-2">
                <button
                  @click="editProduct(product)"
                  class="px-3 py-1 bg-blue-600 hover:bg-blue-700 rounded text-xs font-semibold"
                >
                  ✏️
                </button>
                <button
                  @click="deleteProduct(product)"
                  class="px-3 py-1 bg-red-600 hover:bg-red-700 rounded text-xs font-semibold"
                >
                  🗑️
                </button>
              </td>
            </tr>
            <tr v-if="filteredProducts.length === 0">
              <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                No hay productos para mostrar
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Modal -->
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-slate-800 rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
          <!-- Header -->
          <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-bold text-white">
              {{ editingProduct.id ? '✏️ Editar Producto' : '➕ Nuevo Producto' }}
            </h3>
            <button
              @click="showModal = false"
              class="text-white text-2xl hover:text-gray-300"
            >
              ✕
            </button>
          </div>

          <!-- Body -->
          <div class="p-6 space-y-4">
            <!-- Name -->
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Nombre del Producto</label>
              <input
                v-model="editingProduct.name"
                type="text"
                placeholder="Nombre del producto"
                class="w-full px-4 py-2 rounded-lg bg-slate-700 text-white border border-slate-600 focus:border-blue-500 focus:outline-none"
              />
            </div>

            <!-- Description -->
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Descripción</label>
              <textarea
                v-model="editingProduct.description"
                placeholder="Descripción del producto"
                class="w-full px-4 py-2 rounded-lg bg-slate-700 text-white border border-slate-600 focus:border-blue-500 focus:outline-none resize-none"
                rows="3"
              ></textarea>
            </div>

            <!-- Category -->
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Categoría</label>
              <select
                v-model="editingProduct.category_id"
                class="w-full px-4 py-2 rounded-lg bg-slate-700 text-white border border-slate-600 focus:border-blue-500 focus:outline-none"
              >
                <option value="">Seleccionar categoría</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.name }}
                </option>
              </select>
            </div>

            <!-- Price & Cost -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Precio de Venta</label>
                <input
                  v-model.number="editingProduct.price"
                  type="number"
                  step="0.01"
                  class="w-full px-4 py-2 rounded-lg bg-slate-700 text-white border border-slate-600 focus:border-blue-500 focus:outline-none"
                  placeholder="0.00"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Costo</label>
                <input
                  v-model.number="editingProduct.cost"
                  type="number"
                  step="0.01"
                  class="w-full px-4 py-2 rounded-lg bg-slate-700 text-white border border-slate-600 focus:border-blue-500 focus:outline-none"
                  placeholder="0.00"
                />
              </div>
            </div>

            <!-- Stock -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Stock Disponible</label>
                <input
                  v-model.number="editingProduct.stock"
                  type="number"
                  class="w-full px-4 py-2 rounded-lg bg-slate-700 text-white border border-slate-600 focus:border-blue-500 focus:outline-none"
                  placeholder="0"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Stock Mínimo</label>
                <input
                  v-model.number="editingProduct.min_stock"
                  type="number"
                  class="w-full px-4 py-2 rounded-lg bg-slate-700 text-white border border-slate-600 focus:border-blue-500 focus:outline-none"
                  placeholder="0"
                />
              </div>
            </div>

            <!-- SKU -->
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">SKU</label>
              <input
                v-model="editingProduct.sku"
                type="text"
                placeholder="SKU único"
                class="w-full px-4 py-2 rounded-lg bg-slate-700 text-white border border-slate-600 focus:border-blue-500 focus:outline-none"
              />
            </div>

            <!-- Image Upload -->
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Imagen del Producto</label>
              <div v-if="!editingProduct.id" class="text-sm text-yellow-400 bg-yellow-900 bg-opacity-30 p-3 rounded-lg">
                ⚠️ Guarda el producto primero, luego podrás subir una imagen
              </div>
              <div v-else class="flex gap-4">
                <div v-if="editingProduct.image_path" class="flex-shrink-0">
                  <img :src="editingProduct.image_path" :alt="editingProduct.name" class="h-24 w-24 rounded-lg object-cover" />
                </div>
                <div class="flex-1">
                  <input
                    type="file"
                    @change="handleImageUpload"
                    accept="image/*"
                    class="w-full"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="bg-slate-900 px-6 py-4 flex justify-end gap-3">
            <button
              @click="showModal = false"
              class="px-4 py-2 text-gray-300 hover:text-white transition-colors"
            >
              Cancelar
            </button>
            <button
              @click="saveProduct"
              :disabled="savingProduct"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold disabled:opacity-50 transition-colors"
            >
              {{ savingProduct ? '⏳ Guardando...' : '💾 Guardar' }}
            </button>
          </div>
        </div>
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
const savingProduct = ref(false);
const showModal = ref(false);
const searchQuery = ref('');
const selectedCategory = ref('');

const editingProduct = ref({
  id: null,
  name: '',
  description: '',
  category_id: '',
  price: 0,
  cost: 0,
  stock: 0,
  min_stock: 0,
  sku: '',
  image_path: '',
  active: true,
});

const filteredProducts = computed(() => {
  if (!Array.isArray(products.value)) {
    return [];
  }
  return products.value.filter(product => {
    const matchesSearch = product.name.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesCategory = !selectedCategory.value || product.category_id === parseInt(selectedCategory.value);
    return matchesSearch && matchesCategory;
  });
});

const fetchProducts = async () => {
  try {
    const response = await axios.get('products');
    products.value = response.data.data || response.data;
  } catch (error) {
    console.error('Error fetching products:', error);
    alert('Error al cargar los productos');
  } finally {
    loading.value = false;
  }
};

const fetchCategories = async () => {
  try {
    const response = await axios.get('categories');
    categories.value = response.data.data || response.data;
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

const editProduct = (product) => {
  editingProduct.value = { ...product };
  showModal.value = true;
};

const saveProduct = async () => {
  try {
    savingProduct.value = true;

    if (editingProduct.value.id) {
      // Update
      await axios.put(`products/${editingProduct.value.id}`, editingProduct.value);
      alert('✓ Producto actualizado exitosamente');
      showModal.value = false;
      fetchProducts();
    } else {
      // Create
      const response = await axios.post('products', editingProduct.value);
      const createdProduct = response.data.product;
      
      // Actualizar el producto editando con el ID del producto creado
      editingProduct.value = { ...createdProduct };
      
      alert('✓ Producto creado exitosamente. Ahora puedes subir una imagen.');
      // No cerrar el modal para permitir subir imagen inmediatamente
      fetchProducts();
    }
  } catch (error) {
    console.error('Error saving product:', error);
    alert('❌ Error al guardar el producto: ' + (error.response?.data?.message || error.message));
  } finally {
    savingProduct.value = false;
  }
};

const handleImageUpload = async (event) => {
  const file = event.target.files[0];
  if (!file) return;

  if (!editingProduct.value.id) {
    alert('Por favor guarda el producto primero antes de subir una imagen');
    return;
  }

  try {
    const formData = new FormData();
    formData.append('image', file);

    const response = await axios.post(
      `products/${editingProduct.value.id}/image`,
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      }
    );

    editingProduct.value.image_path = response.data.image_path;
    alert('✓ Imagen actualizada exitosamente');
  } catch (error) {
    console.error('Error uploading image:', error);
    alert('Error al subir la imagen: ' + (error.response?.data?.message || error.message));
  }
};

const toggleActive = async (product) => {
  try {
    await axios.put(`products/${product.id}`, { active: !product.active });
    product.active = !product.active;
    alert('✓ Producto actualizado');
  } catch (error) {
    console.error('Error toggling product status:', error);
    alert('❌ Error al actualizar el producto');
  }
};

const deleteProduct = async (product) => {
  if (!confirm(`¿Estás seguro de que quieres eliminar "${product.name}"?`)) return;

  try {
    await axios.delete(`products/${product.id}`);
    alert('✓ Producto eliminado exitosamente');
    fetchProducts();
  } catch (error) {
    console.error('Error deleting product:', error);
    alert('❌ Error al eliminar el producto');
  }
};

onMounted(() => {
  fetchProducts();
  fetchCategories();
});
</script>
