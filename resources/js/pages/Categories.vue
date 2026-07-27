<template>
  <app-layout>
    <div class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Categorías</h1>
        <p class="text-gray-600 mt-1">Gestiona las categorías de productos</p>
      </div>
      <button @click="showForm = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nueva Categoría
      </button>
    </div>

    <!-- Form Modal -->
    <div v-if="showForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 text-white flex justify-between items-center">
          <h3 class="text-lg font-semibold">{{ editingId ? 'Editar' : 'Nueva' }} Categoría</h3>
          <button @click="showForm = false" class="text-white hover:text-gray-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <form @submit.prevent="saveCategory" class="p-6 space-y-4">
          <input 
            v-model="form.name" 
            type="text" 
            placeholder="Nombre de categoría" 
            required 
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
          <textarea 
            v-model="form.description" 
            placeholder="Descripción" 
            rows="3"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
          <div class="flex gap-3">
            <button type="button" @click="showForm = false" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancelar</button>
            <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Guardar</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="text-left text-sm font-semibold text-gray-700 px-6 py-4">Nombre</th>
              <th class="text-left text-sm font-semibold text-gray-700 px-6 py-4">Descripción</th>
              <th class="text-center text-sm font-semibold text-gray-700 px-6 py-4">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="category in categories" :key="category.id" class="border-b border-gray-100 hover:bg-gray-50">
              <td class="px-6 py-4 font-medium text-gray-900">{{ category.name }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ category.description }}</td>
              <td class="px-6 py-4 text-center space-x-2">
                <button @click="editCategory(category)" class="text-blue-600 hover:text-blue-900 font-medium">Editar</button>
                <button @click="deleteCategory(category.id)" class="text-red-600 hover:text-red-900 font-medium">Eliminar</button>
              </td>
            </tr>
            <tr v-if="!categories || categories.length === 0">
              <td colspan="3" class="py-8 text-center text-gray-500">No hay categorías registradas</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '../layouts/AppLayout.vue';

export default {
  name: 'Categories',
  components: {
    AppLayout
  },
  data() {
    return {
      categories: [],
      showForm: false,
      editingId: null,
      form: {
        name: '',
        description: ''
      }
    };
  },
  async mounted() {
    await this.loadCategories();
  },
  methods: {
    async loadCategories() {
      try {
        const response = await window.axios.get('/categories');
        this.categories = response.data.data || [];
      } catch (error) {
        console.error('Error loading categories:', error);
        if (error.response?.status === 401) {
          this.$router.push('/login');
        }
      }
    },
    editCategory(category) {
      this.editingId = category.id;
      this.form = { ...category };
      this.showForm = true;
    },
    async saveCategory() {
      try {
        const method = this.editingId ? 'put' : 'post';
        const url = this.editingId ? `/categories/${this.editingId}` : '/categories';
        
        await window.axios[method](url, this.form);
        await this.loadCategories();
        this.showForm = false;
        this.resetForm();
      } catch (error) {
        console.error('Error saving category:', error);
        alert('Error al guardar categóría: ' + (error.response?.data?.message || error.message));
      }
    },
    async deleteCategory(id) {
      if (confirm('¿Está seguro de que desea eliminar esta categoría?')) {
        try {
          await window.axios.delete(`/categories/${id}`);
          await this.loadCategories();
        } catch (error) {
          console.error('Error deleting category:', error);
          alert('Error al eliminar categoría: ' + (error.response?.data?.message || error.message));
        }
      }
    },
    resetForm() {
      this.form = { name: '', description: '' };
      this.editingId = null;
    }
  }
};
</script>
