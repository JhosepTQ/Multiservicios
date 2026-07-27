<template>
  <app-layout>
    <div class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Gastos</h1>
        <p class="text-gray-600 mt-1">Registra y analiza los gastos operacionales</p>
      </div>
      <button @click="showForm = true" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nuevo Gasto
      </button>
    </div>

    <!-- Expense Form Modal -->
    <div v-if="showForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-4 text-white flex justify-between items-center">
          <h3 class="text-lg font-semibold">{{ editingId ? 'Editar' : 'Nuevo' }} Gasto</h3>
          <button @click="showForm = false" class="text-white hover:text-gray-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <form @submit.prevent="saveExpense" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Concepto</label>
            <input v-model="form.concept" type="text" placeholder="Descripción del gasto" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
            <select v-model="form.category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
              <option value="">Seleccionar categoría</option>
              <option value="Servicios">Servicios</option>
              <option value="Suministros">Suministros</option>
              <option value="Nómina">Nómina</option>
              <option value="Otros">Otros</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha del Gasto</label>
            <input v-model="form.expense_date" type="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Monto</label>
            <input v-model.number="form.amount" type="number" step="0.01" placeholder="0.00" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" />
          </div>
          <div class="flex gap-3">
            <button type="button" @click="showForm = false" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancelar</button>
            <button type="submit" class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">Guardar</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Expenses Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="text-left text-sm font-semibold text-gray-700 px-6 py-4">Concepto</th>
              <th class="text-left text-sm font-semibold text-gray-700 px-6 py-4">Categoría</th>
              <th class="text-left text-sm font-semibold text-gray-700 px-6 py-4">Fecha</th>
              <th class="text-right text-sm font-semibold text-gray-700 px-6 py-4">Monto</th>
              <th class="text-center text-sm font-semibold text-gray-700 px-6 py-4">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="expense in expenses" :key="expense.id" class="border-b border-gray-100 hover:bg-gray-50">
              <td class="px-6 py-4 font-medium text-gray-900">{{ expense.concept }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ expense.category }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(expense.created_at) }}</td>
              <td class="px-6 py-4 text-right font-semibold text-red-600">{{ formatSoles(expense.amount) }}</td>
              <td class="px-6 py-4 text-center space-x-2">
                <button @click="editExpense(expense)" class="text-blue-600 hover:text-blue-900 font-medium text-sm">Editar</button>
                <button @click="deleteExpense(expense.id)" class="text-red-600 hover:text-red-900 font-medium text-sm">Eliminar</button>
              </td>
            </tr>
            <tr v-if="!expenses || expenses.length === 0">
              <td colspan="5" class="py-8 text-center text-gray-500">No hay gastos registrados</td>
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
  name: 'Expenses',
  components: {
    AppLayout
  },
  data() {
    return {
      expenses: [],
      showForm: false,
      editingId: null,
      form: {
        concept: '',
        category: '',
        expense_date: '',
        amount: 0
      },
      loading: false
    };
  },
  methods: {
    formatSoles,
    formatDate,
    async loadExpenses() {
      try {
        const response = await window.axios.get('/expenses');
        this.expenses = response.data.data || [];
      } catch (error) {
        console.error('Error loading expenses:', error);
        alert('Error al cargar gastos');
      }
    },
    editExpense(expense) {
      this.editingId = expense.id;
      this.form = { ...expense };
      this.showForm = true;
    },
    async saveExpense() {
      if (!this.form.concept || !this.form.category || !this.form.expense_date || !this.form.amount) {
        alert('Llene todos los campos');
        return;
      }

      try {
        this.loading = true;
        const method = this.editingId ? 'put' : 'post';
        const url = this.editingId ? `/expenses/${this.editingId}` : '/expenses';
        
        await window.axios[method](url, this.form);
        await this.loadExpenses();
        this.showForm = false;
        this.resetForm();
        alert(this.editingId ? 'Gasto actualizado' : 'Gasto registrado');
      } catch (error) {
        console.error('Error saving expense:', error);
        alert('Error al guardar gasto: ' + (error.response?.data?.message || error.message));
      } finally {
        this.loading = false;
      }
    },
    async deleteExpense(id) {
      if (confirm('¿Está seguro de que desea eliminar este gasto?')) {
        try {
          await window.axios.delete(`/expenses/${id}`);
          await this.loadExpenses();
          alert('Gasto eliminado');
        } catch (error) {
          console.error('Error deleting expense:', error);
          alert('Error al eliminar gasto');
        }
      }
    },
    resetForm() {
      this.form = { concept: '', category: '', expense_date: '', amount: 0 };
      this.editingId = null;
    }
  },
  async mounted() {
    await this.loadExpenses();
  }
};
</script>

<style scoped>
</style>
