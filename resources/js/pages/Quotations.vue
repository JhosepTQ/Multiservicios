<template>
  <app-layout>
    <div class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Cotizaciones</h1>
        <p class="text-gray-600 mt-1">Crea y gestiona cotizaciones personalizadas en PDF</p>
      </div>
      <button @click="showForm = true" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nueva Cotización
      </button>
    </div>

    <!-- Quotation Form Modal -->
    <div v-if="showForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4 text-white flex justify-between items-center">
          <h3 class="text-lg font-semibold">{{ editingId ? 'Editar' : 'Nueva' }} Cotización</h3>
          <button @click="showForm = false" class="text-white hover:text-gray-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <div class="p-6 space-y-6">
          <!-- Customer Info -->
          <div class="grid grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre Cliente</label>
              <input v-model="form.customer_name" type="text" placeholder="Ej: Juan Pérez" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
              <input v-model="form.customer_email" type="email" placeholder="correo@ejemplo.com" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
              <input v-model="form.customer_phone" type="tel" placeholder="999 999 999" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Válida Hasta</label>
              <input v-model="form.valid_until" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Estado</label>
              <select v-model="form.status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                <option value="pendiente">Pendiente</option>
                <option value="aceptada">Aceptada</option>
                <option value="rechazada">Rechazada</option>
              </select>
            </div>
          </div>

          <!-- Items Table -->
          <div>
            <div class="flex justify-between items-center mb-3">
              <label class="block text-sm font-semibold text-gray-700">Productos/Servicios</label>
              <button @click="addItem" type="button" class="text-sm bg-purple-100 text-purple-700 px-3 py-1 rounded hover:bg-purple-200">+ Agregar Item</button>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full border border-gray-300 rounded-lg">
                <thead class="bg-gray-100 border-b">
                  <tr>
                    <th class="text-left px-3 py-2 text-sm font-semibold text-gray-700">Descripción</th>
                    <th class="text-center px-3 py-2 text-sm font-semibold text-gray-700">Cantidad</th>
                    <th class="text-right px-3 py-2 text-sm font-semibold text-gray-700">Precio Unitario</th>
                    <th class="text-right px-3 py-2 text-sm font-semibold text-gray-700">Subtotal</th>
                    <th class="text-center px-3 py-2 text-sm font-semibold text-gray-700">Acción</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in form.items" :key="index" class="border-b">
                    <td class="px-3 py-2">
                      <input v-model="item.description" type="text" placeholder="Descripción del producto" class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-purple-500" />
                    </td>
                    <td class="px-3 py-2 text-center">
                      <input v-model.number="item.quantity" type="number" min="1" class="w-full px-2 py-1 border border-gray-300 rounded text-sm text-center focus:outline-none focus:ring-2 focus:ring-purple-500" />
                    </td>
                    <td class="px-3 py-2 text-right">
                      <input v-model.number="item.unit_price" type="number" step="0.01" class="w-full px-2 py-1 border border-gray-300 rounded text-sm text-right focus:outline-none focus:ring-2 focus:ring-purple-500" />
                    </td>
                    <td class="px-3 py-2 text-right font-semibold text-gray-900">S/ {{ (item.quantity * item.unit_price).toFixed(2) }}</td>
                    <td class="px-3 py-2 text-center">
                      <button v-if="form.items.length > 1" @click="removeItem(index)" type="button" class="text-red-600 hover:text-red-900 text-sm font-medium">Eliminar</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Total -->
          <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-4 rounded-lg border border-purple-200">
            <div class="flex justify-between items-center">
              <span class="text-lg font-semibold text-gray-900">Total Cotización:</span>
              <span class="text-2xl font-bold text-purple-600">{{ formatSoles(calculateTotal()) }}</span>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-3">
            <button type="button" @click="showForm = false" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancelar</button>
            <button type="button" @click="saveQuotation" :disabled="loading" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-70">{{ loading ? 'Guardando...' : 'Guardar Cotización' }}</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Quotations Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="text-left text-sm font-semibold text-gray-700 px-6 py-4">N° Cotización</th>
              <th class="text-left text-sm font-semibold text-gray-700 px-6 py-4">Cliente</th>
              <th class="text-left text-sm font-semibold text-gray-700 px-6 py-4">Fecha</th>
              <th class="text-right text-sm font-semibold text-gray-700 px-6 py-4">Monto</th>
              <th class="text-center text-sm font-semibold text-gray-700 px-6 py-4">Estado</th>
              <th class="text-center text-sm font-semibold text-gray-700 px-6 py-4">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="quote in quotations" :key="quote.id" class="border-b border-gray-100 hover:bg-gray-50">
              <td class="px-6 py-4 font-medium text-gray-900">{{ quote.quotation_number }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ quote.customer_name }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(quote.created_at) }}</td>
              <td class="px-6 py-4 text-right font-semibold text-gray-900">{{ formatSoles(quote.total) }}</td>
              <td class="px-6 py-4 text-center">
                <span :class="`px-3 py-1 rounded-full text-xs font-medium ${quote.status === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : quote.status === 'aceptada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`">{{ quote.status }}</span>
              </td>
              <td class="px-6 py-4 text-center space-x-2">
                <button @click="downloadPdf(quote.id)" class="text-purple-600 hover:text-purple-900 font-medium text-sm">PDF</button>
                <button @click="editQuotation(quote)" class="text-blue-600 hover:text-blue-900 font-medium text-sm">Editar</button>
                <button @click="deleteQuotation(quote.id)" class="text-red-600 hover:text-red-900 font-medium text-sm">Eliminar</button>
              </td>
            </tr>
            <tr v-if="!quotations || quotations.length === 0">
              <td colspan="6" class="py-8 text-center text-gray-500">No hay cotizaciones</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '../layouts/AppLayout.vue';
import { formatSoles, formatDate } from '../utils/format.js';
import { downloadPdf } from '../utils/pdf.js';

export default {
  name: 'Quotations',
  components: {
    AppLayout
  },
  data() {
    return {
      quotations: [],
      showForm: false,
      editingId: null,
      form: {
        customer_name: '',
        customer_email: '',
        customer_phone: '',
        valid_until: '',
        status: 'pendiente',
        items: [{ product_id: '', quantity: 1, unit_price: 0, description: '' }]
      },
      products: [],
      loading: false
    };
  },
  methods: {
    formatSoles,
    formatDate,
    calculateTotal() {
      return this.form.items.reduce((total, item) => {
        return total + (item.quantity * item.unit_price);
      }, 0);
    },
    async loadQuotations() {
      try {
        const response = await window.axios.get('/quotations');
        this.quotations = response.data.data || [];
      } catch (error) {
        console.error('Error loading quotations:', error);
      }
    },
    async loadProducts() {
      try {
        const response = await window.axios.get('/products');
        this.products = response.data.data || [];
      } catch (error) {
        console.error('Error loading products:', error);
      }
    },
    async saveQuotation() {
      if (!this.form.customer_name) {
        alert('Ingrese el nombre del cliente');
        return;
      }
      if (!this.form.valid_until) {
        alert('Ingrese fecha de validez');
        return;
      }
      if (this.form.items.filter(i => i.description || i.quantity > 0).length === 0) {
        alert('Agregue al menos un item');
        return;
      }

      try {
        this.loading = true;
        const method = this.editingId ? 'put' : 'post';
        const url = this.editingId ? `/quotations/${this.editingId}` : '/quotations';
        
        const quotationData = {
          customer_name: this.form.customer_name,
          customer_email: this.form.customer_email,
          customer_phone: this.form.customer_phone,
          valid_until: this.form.valid_until,
          status: this.form.status,
          items: this.form.items.filter(i => i.description || i.quantity > 0)
        };
        
        await window.axios[method](url, quotationData);
        await this.loadQuotations();
        this.showForm = false;
        this.resetForm();
        alert(this.editingId ? 'Cotización actualizada' : 'Cotización creada');
      } catch (error) {
        console.error('Error saving quotation:', error);
        alert('Error al guardar cotización: ' + (error.response?.data?.message || error.message));
      } finally {
        this.loading = false;
      }
    },
    async downloadPdf(quotationId) {
      try {
        const response = await window.axios.get(`/quotations/${quotationId}/pdf`, { responseType: 'blob' });
        const url = window.URL.createObjectURL(response.data);
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `cotizacion-${quotationId}.pdf`);
        document.body.appendChild(link);
        link.click();
      } catch (error) {
        console.error('Error downloading PDF:', error);
        alert('Error al descargar PDF');
      }
    },
    editQuotation(quotation) {
      this.editingId = quotation.id;
      this.form = { ...quotation };
      this.showForm = true;
    },
    async deleteQuotation(id) {
      if (confirm('¿Está seguro de que desea eliminar esta cotización?')) {
        try {
          await window.axios.delete(`/quotations/${id}`);
          await this.loadQuotations();
          alert('Cotización eliminada');
        } catch (error) {
          console.error('Error deleting quotation:', error);
          alert('Error al eliminar cotización');
        }
      }
    },
    addItem() {
      this.form.items.push({ product_id: '', quantity: 1, unit_price: 0, description: '' });
    },
    removeItem(index) {
      if (this.form.items.length > 1) {
        this.form.items.splice(index, 1);
      }
    },
    resetForm() {
      this.form = {
        customer_name: '',
        customer_email: '',
        customer_phone: '',
        valid_until: '',
        status: 'pendiente',
        items: [{ product_id: '', quantity: 1, unit_price: 0, description: '' }]
      };
      this.editingId = null;
    }
  },
  async mounted() {
    await this.loadQuotations();
    await this.loadProducts();
  }
};
</script>

<style scoped>
</style>
