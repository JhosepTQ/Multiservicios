<template>
  <app-layout>
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
      <p class="text-gray-600 mt-2">Bienvenido al panel de control de MultiService</p>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Total Sales -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm font-medium">Ventas Totales</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatSoles(totalSales) }}</p>
            <p class="text-green-600 text-sm mt-2">↑ 12% vs mes anterior</p>
          </div>
          <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Total Expenses -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm font-medium">Gastos Totales</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatSoles(totalExpenses) }}</p>
            <p class="text-red-600 text-sm mt-2">↑ 5% vs mes anterior</p>
          </div>
          <div class="w-12 h-12 bg-gradient-to-br from-red-400 to-red-600 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Profit -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm font-medium">Ganancia Neta</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatSoles(profit) }}</p>
            <p :class="profit > 0 ? 'text-green-600' : 'text-red-600'" class="text-sm mt-2">
              {{ profit > 0 ? '✓ Positivo' : '✗ Negativo' }}
            </p>
          </div>
          <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Total Products -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm font-medium">Productos Activos</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ totalProducts }}</p>
            <p class="text-blue-600 text-sm mt-2">{{ lowStockProducts }} con stock bajo</p>
          </div>
          <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10m0-10l-8 4" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
      <!-- Sales Chart -->
      <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-lg font-semibold text-gray-900">Ventas del Mes</h3>
          <select class="px-3 py-1 bg-gray-100 rounded-lg text-sm text-gray-700 border-0">
            <option>Últimos 30 días</option>
            <option>Últimos 7 días</option>
            <option>Este mes</option>
          </select>
        </div>
        <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
          <div class="text-center">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <p class="text-gray-500 text-sm">Gráfico de ventas (integrar Chart.js)</p>
          </div>
        </div>
      </div>

      <!-- Recent Stats -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Estadísticas Rápidas</h3>
        <div class="space-y-4">
          <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
            <span class="text-sm text-gray-700">Transacciones hoy</span>
            <span class="font-bold text-lg text-blue-600">{{ dailyTransactions }}</span>
          </div>
          <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
            <span class="text-sm text-gray-700">Ticket promedio</span>
            <span class="font-bold text-lg text-green-600">${{ averageTicket }}</span>
          </div>
          <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
            <span class="text-sm text-gray-700">Clientes atendidos</span>
            <span class="font-bold text-lg text-purple-600">{{ customersServed }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Sales Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900">Últimas Ventas</h3>
        <router-link to="/sales" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Ver todas →</router-link>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-200">
              <th class="text-left text-sm font-semibold text-gray-700 py-3">N° Boleta</th>
              <th class="text-left text-sm font-semibold text-gray-700 py-3">Fecha</th>
              <th class="text-left text-sm font-semibold text-gray-700 py-3">Cliente</th>
              <th class="text-left text-sm font-semibold text-gray-700 py-3">Monto</th>
              <th class="text-left text-sm font-semibold text-gray-700 py-3">Estado</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="sale in recentSales" :key="sale.id" class="border-b border-gray-100 hover:bg-gray-50">
              <td class="py-3 text-sm font-medium text-gray-900">{{ sale.receipt_number }}</td>
              <td class="py-3 text-sm text-gray-600">{{ formatDate(sale.created_at) }}</td>
              <td class="py-3 text-sm text-gray-600">Cliente {{ sale.id }}</td>
              <td class="py-3 text-sm font-semibold text-gray-900">{{ formatSoles(sale.total) }}</td>
              <td class="py-3 text-sm">
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Completada</span>
              </td>
            </tr>
            <tr v-if="!recentSales || recentSales.length === 0">
              <td colspan="5" class="py-8 text-center text-gray-500">No hay ventas registradas aún</td>
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

export default {
  name: 'Dashboard',
  components: {
    AppLayout
  },
  data() {
    return {
      totalSales: 0,
      totalExpenses: 0,
      totalProducts: 0,
      lowStockProducts: 0,
      recentSales: [],
      dailyTransactions: 0,
      averageTicket: 0,
      customersServed: 0
    };
  },
  computed: {
    profit() {
      return this.totalSales - this.totalExpenses;
    }
  },
  methods: {
    formatSoles,
    formatDate
  },
  async mounted() {
    try {
      const response = await window.axios.get('/dashboard/summary');
      const data = response.data;
      
      this.totalSales = data.total_sales || 0;
      this.totalExpenses = data.total_expenses || 0;
      this.totalProducts = data.total_products || 0;
      this.lowStockProducts = data.low_stock_products || 0;
      this.recentSales = data.recent_sales || [];
      this.dailyTransactions = data.daily_transactions || 0;
      this.averageTicket = data.average_ticket || 0;
      this.customersServed = data.customers_served || 0;
    } catch (error) {
      console.error('Error loading dashboard:', error);
      if (error.response?.status === 401) {
        this.$router.push('/login');
      }
    }
  }
};
</script>

<style scoped>
</style>
