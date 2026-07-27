import { createRouter, createWebHistory } from 'vue-router';

// Pages
import Dashboard from '../pages/Dashboard.vue';
import Categories from '../pages/Categories.vue';
import Products from '../pages/Products.vue';
import Sales from '../pages/Sales.vue';
import Expenses from '../pages/Expenses.vue';
import Quotations from '../pages/Quotations.vue';
import Reports from '../pages/Reports.vue';
import Login from '../pages/Login.vue';
import Catalog from '../pages/Catalog.vue';

const routes = [
  { path: '/login', component: Login, name: 'Login', meta: { requiresAuth: false } },
  { path: '/catalog', component: Catalog, name: 'Catalog', meta: { requiresAuth: false } },
  { path: '/dashboard', component: Dashboard, name: 'Dashboard', meta: { requiresAuth: true } },
  { path: '/categories', component: Categories, name: 'Categories', meta: { requiresAuth: true } },
  { path: '/products', component: Products, name: 'Products', meta: { requiresAuth: true } },
  { path: '/sales', component: Sales, name: 'Sales', meta: { requiresAuth: true } },
  { path: '/expenses', component: Expenses, name: 'Expenses', meta: { requiresAuth: true } },
  { path: '/quotations', component: Quotations, name: 'Quotations', meta: { requiresAuth: true } },
  { path: '/reports', component: Reports, name: 'Reports', meta: { requiresAuth: true } },
  { path: '/', redirect: '/dashboard' }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Navigation guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('auth_token');
  const requiresAuth = to.meta.requiresAuth !== false; // Por defecto requiere autenticación

  if (to.path === '/login' && token) {
    next('/dashboard');
  } else if (requiresAuth && !token) {
    next('/login');
  } else {
    next();
  }
});

export default router;
