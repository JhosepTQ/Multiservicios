<template>
  <app-layout>
    <div class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Ventas</h1>
        <p class="text-gray-600 mt-1">Registra y gestiona tus boletas de venta</p>
      </div>
      <button @click="showNewSale = true" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nueva Venta
      </button>
    </div>

    <!-- New Sale Modal -->
    <div v-if="showNewSale" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4 text-white flex justify-between items-center">
          <h3 class="text-lg font-semibold">Nueva Venta / Boleta</h3>
          <button @click="showNewSale = false" class="text-white hover:text-gray-200">
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
              <input v-model="form.customer_name" type="text" placeholder="Ej: Juan Pérez" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
              <input v-model="form.customer_email" type="email" placeholder="correo@ejemplo.com" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
              <input v-model="form.customer_phone" type="tel" placeholder="999 999 999" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
            </div>
          </div>

          <!-- Items Table -->
          <div>
            <div class="flex justify-between items-center mb-3">
              <label class="block text-sm font-semibold text-gray-700">Productos/Servicios</label>
              <button @click="addItem" type="button" class="text-sm bg-green-100 text-green-700 px-3 py-1 rounded hover:bg-green-200">+ Agregar Item</button>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full border border-gray-300 rounded-lg">
                <thead class="bg-gray-100 border-b">
                  <tr>
                    <th class="text-left px-3 py-2 text-sm font-semibold text-gray-700">Producto</th>
                    <th class="text-center px-3 py-2 text-sm font-semibold text-gray-700">Cantidad</th>
                    <th class="text-right px-3 py-2 text-sm font-semibold text-gray-700">Precio Unitario</th>
                    <th class="text-right px-3 py-2 text-sm font-semibold text-gray-700">Subtotal</th>
                    <th class="text-center px-3 py-2 text-sm font-semibold text-gray-700">Acción</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in form.items" :key="index" class="border-b">
                    <td class="px-3 py-2">
                      <select v-model="item.product_id" @change="onProductChange(index)" class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Seleccionar producto</option>
                        <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
                      </select>
                    </td>
                    <td class="px-3 py-2 text-center">
                      <input v-model.number="item.quantity" @input="recalculateSubtotal(index)" type="number" min="1" class="w-full px-2 py-1 border border-gray-300 rounded text-sm text-center focus:outline-none focus:ring-2 focus:ring-green-500" />
                    </td>
                    <td class="px-3 py-2">
                      <input v-model.number="item.unit_price" @input="recalculateSubtotal(index)" type="number" step="0.01" min="0" class="w-full px-2 py-1 border border-gray-300 rounded text-sm text-right focus:outline-none focus:ring-2 focus:ring-green-500" />
                    </td>
                    <td class="px-3 py-2 text-right font-semibold text-gray-900">S/ {{ (item.subtotal || 0).toFixed(2) }}</td>
                    <td class="px-3 py-2 text-center">
                      <button v-if="form.items.length > 1" @click="removeItem(index)" type="button" class="text-red-600 hover:text-red-900 text-sm font-medium">Eliminar</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Observations / Discount -->  
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Observaciones / Descuento</label>
            <textarea v-model="form.notes" rows="2" placeholder="Ej: Descuento del 10% aplicado, cliente frecuente..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm resize-none"></textarea>
          </div>

          <!-- Total -->
          <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-lg border border-green-200">
            <div class="flex justify-between items-center">
              <span class="text-lg font-semibold text-gray-900">Total a Pagar:</span>
              <span class="text-2xl font-bold text-green-600">{{ formatSoles(calculateTotal()) }}</span>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-3">
            <button type="button" @click="showNewSale = false" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancelar</button>
            <button type="button" @click="saveSale" :disabled="loading" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-70">{{ loading ? 'Guardando...' : 'Guardar Venta' }}</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="text-left text-sm font-semibold text-gray-700 px-6 py-4">N° Boleta</th>
              <th class="text-left text-sm font-semibold text-gray-700 px-6 py-4">Fecha</th>
              <th class="text-left text-sm font-semibold text-gray-700 px-6 py-4">Cliente</th>
              <th class="text-right text-sm font-semibold text-gray-700 px-6 py-4">Monto</th>
              <th class="text-center text-sm font-semibold text-gray-700 px-6 py-4">Estado</th>
              <th class="text-center text-sm font-semibold text-gray-700 px-6 py-4">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="sale in sales" :key="sale.id" class="border-b border-gray-100 hover:bg-gray-50">
              <td class="px-6 py-4 font-medium text-gray-900">{{ sale.receipt_number }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(sale.created_at) }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ sale.customer_name }}</td>
              <td class="px-6 py-4 text-right font-semibold text-gray-900">{{ formatSoles(sale.total) }}</td>
              <td class="px-6 py-4 text-center">
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Completada</span>
              </td>
              <td class="px-6 py-4 text-center space-x-2">
                <button @click="downloadPdf(sale.id)" class="text-green-600 hover:text-green-900 font-medium text-sm">PDF</button>
                <button @click="deleteSale(sale.id)" class="text-red-600 hover:text-red-900 font-medium text-sm">Eliminar</button>
              </td>
            </tr>
            <tr v-if="!sales || sales.length === 0">
              <td colspan="6" class="py-8 text-center text-gray-500">No hay ventas registradas</td>
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
  name: 'Sales',
  components: {
    AppLayout
  },
  data() {
    return {
      sales: [],
      showNewSale: false,
      form: {
        customer_name: '',
        customer_phone: '',
        customer_email: '',
        items: [{ product_id: '', quantity: 1, unit_price: 0, subtotal: 0 }],
        notes: ''
      },
      products: [],
      loading: false
    };
  },
  methods: {
    formatSoles,
    formatDate,
    onProductChange(index) {
      const item = this.form.items[index];
      const product = this.products.find(p => p.id === item.product_id);
      if (product) {
        item.unit_price = parseFloat(product.price);
        item.subtotal = item.quantity * item.unit_price;
      } else {
        item.unit_price = 0;
        item.subtotal = 0;
      }
    },
    recalculateSubtotal(index) {
      const item = this.form.items[index];
      item.subtotal = (item.quantity || 0) * (item.unit_price || 0);
    },
    calculateTotal() {
      return this.form.items.reduce((total, item) => {
        return total + (item.subtotal || 0);
      }, 0);
    },
    async loadSales() {
      try {
        const response = await window.axios.get('/sales');
        this.sales = response.data.data || [];
      } catch (error) {
        console.error('Error loading sales:', error);
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
    async saveSale() {
      if (!this.form.customer_name) {
        alert('Ingrese el nombre del cliente');
        return;
      }
      if (this.form.items.filter(i => i.product_id).length === 0) {
        alert('Agregue al menos un producto');
        return;
      }

      try {
        this.loading = true;
        const saleData = {
          customer_name: this.form.customer_name,
          customer_email: this.form.customer_email,
          customer_phone: this.form.customer_phone,
          notes: this.form.notes,
          items: this.form.items.filter(i => i.product_id).map(item => ({
            product_id: item.product_id,
            quantity: item.quantity,
            price: item.unit_price
          }))
        };
        
        await window.axios.post('/sales', saleData);
        await this.loadSales();
        this.showNewSale = false;
        this.resetForm();
        alert('Venta registrada correctamente');
      } catch (error) {
        console.error('Error saving sale:', error);
        alert('Error al guardar venta: ' + (error.response?.data?.message || error.message));
      } finally {
        this.loading = false;
      }
    },
    async downloadPdf(saleId) {
      try {
        const response = await window.axios.get(`/sales/${saleId}/pdf`, { responseType: 'blob' });
        const url = window.URL.createObjectURL(response.data);
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `boleta-${saleId}.pdf`);
        document.body.appendChild(link);
        link.click();
      } catch (error) {
        console.error('Error downloading PDF:', error);
        alert('Error al descargar PDF');
      }
    },
    async deleteSale(id) {
      if (confirm('¿Está seguro de que desea eliminar esta venta?')) {
        try {
          await window.axios.delete(`/sales/${id}`);
          await this.loadSales();
          alert('Venta eliminada');
        } catch (error) {
          console.error('Error deleting sale:', error);
          alert('Error al eliminar venta');
        }
      }
    },
    addItem() {
      this.form.items.push({ product_id: '', quantity: 1, unit_price: 0, subtotal: 0 });
    },
    removeItem(index) {
      if (this.form.items.length > 1) {
        this.form.items.splice(index, 1);
      }
    },
    resetForm() {
      this.form = {
        customer_name: '',
        customer_phone: '',
        customer_email: '',
        notes: '',
        items: [{ product_id: '', quantity: 1, unit_price: 0, subtotal: 0 }]
      };
    }
  },
  async mounted() {
    await this.loadSales();
    await this.loadProducts();
  }
};
</script>

<style scoped>
</style>
