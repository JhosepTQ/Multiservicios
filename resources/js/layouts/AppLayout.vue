<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="w-64 bg-gradient-to-b from-slate-900 to-slate-800 text-white shadow-lg hidden md:flex flex-col">
      <div class="p-6 border-b border-slate-700">
        <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">
          MultiService
        </h1>
        <p class="text-xs text-slate-400 mt-1">Sistema Empresarial</p>
      </div>

      <nav class="flex-1 p-4 space-y-2">
        <router-link 
          to="/dashboard" 
          class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all" 
          :class="{ 'bg-blue-600 text-white': isActive('dashboard'), 'hover:bg-slate-700 text-slate-300': !isActive('dashboard') }"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11v-5m0 0V9m0 5h-2m2 0h2" />
          </svg>
          <span>Dashboard</span>
        </router-link>

        <router-link 
          to="/categories" 
          class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all"
          :class="{ 'bg-blue-600 text-white': isActive('categories'), 'hover:bg-slate-700 text-slate-300': !isActive('categories') }"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
          </svg>
          <span>Categorías</span>
        </router-link>

        <router-link 
          to="/products" 
          class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all"
          :class="{ 'bg-blue-600 text-white': isActive('products'), 'hover:bg-slate-700 text-slate-300': !isActive('products') }"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10m0-10l-8 4" />
          </svg>
          <span>Productos</span>
        </router-link>

        <router-link 
          to="/sales" 
          class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all"
          :class="{ 'bg-blue-600 text-white': isActive('sales'), 'hover:bg-slate-700 text-slate-300': !isActive('sales') }"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Ventas</span>
        </router-link>

        <router-link 
          to="/quotations" 
          class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all"
          :class="{ 'bg-blue-600 text-white': isActive('quotations'), 'hover:bg-slate-700 text-slate-300': !isActive('quotations') }"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <span>Cotizaciones</span>
        </router-link>

        <router-link 
          to="/expenses" 
          class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all"
          :class="{ 'bg-blue-600 text-white': isActive('expenses'), 'hover:bg-slate-700 text-slate-300': !isActive('expenses') }"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
          <span>Gastos</span>
        </router-link>

        <router-link 
          to="/reports" 
          class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all"
          :class="{ 'bg-blue-600 text-white': isActive('reports'), 'hover:bg-slate-700 text-slate-300': !isActive('reports') }"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
          <span>Reportes</span>
        </router-link>
      </nav>

      <div class="p-4 border-t border-slate-700">
        <button @click="logout" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg bg-red-600 hover:bg-red-700 transition-all text-white">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          <span>Salir</span>
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Navbar -->
      <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="flex items-center justify-between px-6 py-4">
          <button @click="toggleSidebar" class="md:hidden text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          <div class="flex-1"></div>
          <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2">
              <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                A
              </div>
              <div>
                <p class="text-sm font-medium text-gray-900">Admin</p>
                <p class="text-xs text-gray-500">Administrador</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Content Area -->
      <div class="flex-1 overflow-auto">
        <div class="p-8">
          <slot />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AppLayout',
  methods: {
    isActive(route) {
      return this.$route.name && this.$route.name.toLowerCase().includes(route.toLowerCase());
    },
    toggleSidebar() {
      // Mobile sidebar toggle
    },
    logout() {
      localStorage.removeItem('auth_token');
      this.$router.push('/login');
    }
  }
};
</script>

<style scoped>
</style>
