<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 flex items-center justify-center p-4">
    <!-- Decorative elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 -top-40 -right-40"></div>
      <div class="absolute w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 -bottom-40 -left-40"></div>
    </div>

    <!-- Login Card -->
    <div class="relative w-full max-w-md">
      <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-8 py-12 text-center">
          <h1 class="text-4xl font-bold text-white mb-2">MultiService</h1>
          <p class="text-blue-100 text-sm">Sistema de Gestión Empresarial</p>
        </div>

        <!-- Form -->
        <div class="px-8 py-8">
          <form @submit.prevent="login" class="space-y-6">
            <!-- Email -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Correo Electrónico</label>
              <input 
                v-model="email" 
                type="email" 
                placeholder="admin@multiservice.com"
                required
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none transition-colors bg-gray-50 hover:bg-white"
              />
            </div>

            <!-- Password -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Contraseña</label>
              <input 
                v-model="password" 
                type="password" 
                placeholder="••••••••"
                required
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none transition-colors bg-gray-50 hover:bg-white"
              />
            </div>

            <!-- Remember me -->
            <div class="flex items-center">
              <input type="checkbox" id="remember" class="w-4 h-4 text-blue-600">
              <label for="remember" class="ml-2 text-sm text-gray-600">Recuérdame</label>
            </div>

            <!-- Error Alert -->
            <div v-if="error" class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
              <p class="text-red-700 text-sm">{{ error }}</p>
            </div>

            <!-- Submit Button -->
            <button 
              type="submit"
              :disabled="loading"
              class="w-full bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-semibold py-3 rounded-lg hover:shadow-lg transition-all duration-300 disabled:opacity-70"
            >
              <span v-if="!loading">Iniciar Sesión</span>
              <span v-else>Verificando...</span>
            </button>
          </form>

          <!-- Footer -->
          <div class="mt-8 pt-6 border-t border-gray-200 text-center">
            <p class="text-sm text-gray-600">
              ¿Credenciales de prueba?
              <button 
                type="button" 
                @click="fillTestCredentials"
                class="text-blue-600 hover:text-blue-700 font-semibold"
              >
                Usar Demo
              </button>
            </p>
          </div>
        </div>
      </div>

      <!-- Support Info -->
      <div class="mt-6 text-center text-gray-300 text-sm">
        <p>© 2024 MultiService. Todos los derechos reservados.</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Login',
  data() {
    return {
      email: '',
      password: '',
      error: '',
      loading: false
    };
  },
  methods: {
    fillTestCredentials() {
      this.email = 'admin@multiservice.com';
      this.password = 'password123';
    },
    async login() {
      if (!this.email || !this.password) {
        this.error = 'Por favor completa todos los campos';
        return;
      }

      this.loading = true;
      this.error = '';

      try {
        const response = await window.axios.post('/login', {
          email: this.email,
          password: this.password
        });
        
        if (response.data && response.data.token) {
          const token = response.data.token;
          localStorage.setItem('auth_token', token);
          // Set authorization header in axios interceptor
          window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
          this.$router.push('/dashboard');
        } else {
          this.error = response.data?.message || 'Error en la respuesta del servidor';
        }
      } catch (err) {
        if (err.response?.status === 401) {
          this.error = 'Credenciales inválidas';
        } else if (err.response?.data?.message) {
          this.error = err.response.data.message;
        } else {
          this.error = 'Error de conexión. Verifica que el servidor esté corriendo.';
        }
        console.error(err);
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
</style>
