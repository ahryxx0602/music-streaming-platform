import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/authStore';

const routes = [
  {
    path: '/',
    component: () => import('../layouts/MainLayout.vue'),
    children: [
      {
        path: '',
        name: 'Home',
        component: () => import('../views/Explore/HomeView.vue')
      },
      // Thêm các route Library, Explore tại đây
    ]
  },
  {
    path: '/auth',
    component: () => import('../layouts/AuthLayout.vue'),
    children: [
      {
        path: 'login',
        name: 'Login',
        component: () => import('../views/Auth/LoginView.vue')
      }
    ]
  },
  {
    path: '/artist',
    component: () => import('../layouts/ArtistLayout.vue'),
    meta: { requiresAuth: true, role: 'artist' },
    children: [
      {
        path: 'dashboard',
        name: 'ArtistDashboard',
        component: () => import('../views/Artist/DashboardView.vue')
      }
    ]
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation Guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'Login' });
  } else if (to.meta.role && authStore.user?.role !== to.meta.role) {
    next({ name: 'Home' }); // Sai Role thì đá về Home
  } else {
    next();
  }
});

export default router;
