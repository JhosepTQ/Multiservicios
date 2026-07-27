<template>
  <app-layout>
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Reportes</h1>
      <p class="text-gray-600 mt-1">Análisis financiero y de operaciones</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
        <p class="text-blue-100 text-sm font-medium">Ingresos Este Mes</p>
        <p class="text-4xl font-bold mt-2">{{ formatSoles(monthlyRevenue) }}</p>
        <p class="text-blue-100 text-xs mt-2">Basado en ventas registradas</p>
      </div>
      <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-6 text-white shadow-lg">
        <p class="text-red-100 text-sm font-medium">Gastos Este Mes</p>
        <p class="text-4xl font-bold mt-2">{{ formatSoles(monthlyExpenses) }}</p>
        <p class="text-red-100 text-xs mt-2">Basado en gastos registrados</p>
      </div>
      <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
        <p class="text-green-100 text-sm font-medium">Ganancia Neta</p>
        <p class="text-4xl font-bold mt-2">{{ formatSoles(monthlyProfit) }}</p>
        <p class="text-green-100 text-xs mt-2" :class="monthlyProfit >= 0 ? 'text-green-100' : 'text-red-100'">{{ monthlyProfit >= 0 ? 'Estado Positivo' : 'Estado Negativo' }}</p>
      </div>
    </div>

    <!-- Reports Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Sales Summary -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
          Resumen de Ventas
        </h3>
        <div class="space-y-4">
          <div class="flex justify-between text-sm">
            <span class="text-gray-600">Total de Ventas</span>
            <span class="font-semibold text-gray-900">{{ totalSales }}</span>
          </div>
          <div class="flex justify-between text-sm">
            <span class="text-gray-600">Venta Promedio</span>
            <span class="font-semibold text-gray-900">{{ formatSoles(averageSale) }}</span>
          </div>
          <div class="flex justify-between text-sm">
            <span class="text-gray-600">Venta Máxima</span>
            <span class="font-semibold text-gray-900">{{ formatSoles(maxSale) }}</span>
          </div>
          <div class="border-t border-gray-200 pt-4 flex justify-between">
            <span class="text-sm font-semibold text-gray-900">Total Ingresos</span>
            <span class="font-bold text-lg text-blue-600">{{ formatSoles(monthlyRevenue) }}</span>
          </div>
        </div>
      </div>

      <!-- Inventory Status -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10m0-10l-8 4" />
          </svg>
          Estado del Stock
        </h3>
        <div class="space-y-3">
          <div v-for="product in lowStockProducts" :key="product.id" class="flex items-center justify-between p-3 rounded-lg" :class="product.stock < 5 ? 'bg-red-50 border border-red-200' : 'bg-yellow-50 border border-yellow-200'">
            <div>
              <p class="text-sm font-medium text-gray-900">{{ product.name }}</p>
              <p class="text-xs text-gray-600">Stock: {{ product.stock }} unidades</p>
            </div>
            <span :class="`px-3 py-1 rounded-full text-xs font-semibold ${product.stock < 5 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'}`">
              {{ product.stock < 5 ? 'CRÍTICO' : 'BAJO' }}
            </span>
          </div>
          <div v-if="lowStockProducts.length === 0" class="text-center text-gray-500 text-sm py-4">
            Todos los productos tienen stock adecuado
          </div>
        </div>
      </div>

      <!-- Profit & Loss -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          Estado Financiero
        </h3>
        <div class="space-y-4">
          <div class="flex justify-between text-sm">
            <span class="text-gray-600">Ingresos Totales</span>
            <span class="font-semibold text-gray-900">{{ formatSoles(monthlyRevenue) }}</span>
          </div>
          <div class="flex justify-between text-sm">
            <span class="text-gray-600">Gastos Operacionales</span>
            <span class="font-semibold text-gray-900">{{ formatSoles(monthlyExpenses) }}</span>
          </div>
          <div class="border-t border-gray-200 pt-4 flex justify-between">
            <span class="text-sm font-semibold text-gray-900">Ganancia Neta</span>
            <span :class="`font-bold text-lg ${monthlyProfit >= 0 ? 'text-green-600' : 'text-red-600'}`">{{ formatSoles(monthlyProfit) }}</span>
          </div>
        </div>
      </div>

      <!-- Top Products -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
          Resumen General
        </h3>
        <div class="space-y-3">
          <div class="flex items-center justify-between p-2 border-b">
            <span class="text-sm text-gray-600">Total Productos</span>
            <span class="font-semibold text-gray-900">{{ totalProducts }}</span>
          </div>
          <div class="flex items-center justify-between p-2 border-b">
            <span class="text-sm text-gray-600">Total Categorías</span>
            <span class="font-semibold text-gray-900">{{ totalCategories }}</span>
          </div>
          <div class="flex items-center justify-between p-2">
            <span class="text-sm text-gray-600">Stock Total</span>
            <span class="font-semibold text-gray-900">{{ totalInventory }} unidades</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Export Section -->
    <div class="mt-8 bg-blue-50 rounded-xl p-6 border border-blue-200">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Descargar Reportes en PDF</h3>
      <div class="flex flex-wrap gap-3">
        <button @click="downloadProfitLossReport" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
          Reporte P&L
        </button>
        <button @click="downloadInventoryReport" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
          Reporte Inventario
        </button>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '../layouts/AppLayout.vue';
import { formatSoles } from '../utils/format.js';

export default {
  name: 'Reports',
  components: {
    AppLayout
  },
  data() {
    return {
      monthlyRevenue: 0,
      monthlyExpenses: 0,
      totalSales: 0,
      averageSale: 0,
      maxSale: 0,
      lowStockProducts: [],
      totalProducts: 0,
      totalCategories: 0,
      totalInventory: 0,
      loading: false
    };
  },
  computed: {
    monthlyProfit() {
      return this.monthlyRevenue - this.monthlyExpenses;
    }
  },
  methods: {
    formatSoles,
    async loadReportData() {
      try {
        this.loading = true;
        
        // Load sales data - comentado temporalmente
        // try {
        //   const salesResponse = await window.axios.get('/reports/sales');
        //   ...
        // }
        
        // Load expenses data - comentado temporalmente
        // try {
        //   const expensesResponse = await window.axios.get('/reports/expenses');
        //   ...
        // }
        
        // Load products and inventory
        try {
          const productsResponse = await window.axios.get('/products');
          // Manejar tanto respuesta paginada como array directo
          let products = Array.isArray(productsResponse.data) 
            ? productsResponse.data 
            : (productsResponse.data.data || []);
          
          this.totalProducts = products.length;
          this.totalInventory = products.reduce((sum, p) => sum + (parseInt(p.stock) || 0), 0);
          this.lowStockProducts = products.filter(p => (p.stock || 0) < 10).slice(0, 5);
          
          console.log('✓ Products loaded:', {
            count: this.totalProducts,
            inventory: this.totalInventory,
            lowStock: this.lowStockProducts.length
          });
        } catch (e) {
          console.warn('Products loading error:', e);
          this.totalProducts = 0;
          this.totalInventory = 0;
        }
        
        // Load categories
        try {
          const categoriesResponse = await window.axios.get('/categories');
          // Manejar tanto respuesta paginada como array directo
          let categories = Array.isArray(categoriesResponse.data) 
            ? categoriesResponse.data 
            : (categoriesResponse.data.data || []);
          
          this.totalCategories = categories.length;
          console.log('✓ Categories loaded:', this.totalCategories);
        } catch (e) {
          console.warn('Categories loading error:', e);
          this.totalCategories = 0;
        }
      } catch (error) {
        console.error('Error loading report data:', error);
      } finally {
        this.loading = false;
      }
    },
    async downloadProfitLossReport() {
      try {
        const response = await window.axios.get('/pdf/profit-loss', { responseType: 'blob' });
        const url = window.URL.createObjectURL(response.data);
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `reporte-p-l-${new Date().getTime()}.pdf`);
        document.body.appendChild(link);
        link.click();
      } catch (error) {
        console.error('Error downloading P&L report:', error);
        alert('Error al descargar reporte');
      }
    },
    async downloadInventoryReport() {
      try {
        const response = await window.axios.get('/pdf/inventory', { responseType: 'blob' });
        const url = window.URL.createObjectURL(response.data);
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `reporte-inventario-${new Date().getTime()}.pdf`);
        document.body.appendChild(link);
        link.click();
      } catch (error) {
        console.error('Error downloading inventory report:', error);
        alert('Error al descargar reporte');
      }
    }
  },
  async mounted() {
    await this.loadReportData();
  }
};
</script>

<style scoped>
</style>
