import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/authStore'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/about',
      name: 'about',
      component: () => import('../views/AboutView.vue'),
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/auth/LoginView.vue'),
      meta: { layout: 'AuthLayout', requiresGuest: true },
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('../views/auth/RegisterListenerView.vue'),
      meta: { layout: 'AuthLayout', requiresGuest: true },
    },
    {
      path: '/artist-register',
      name: 'artist-register',
      component: () => import('../views/auth/RegisterArtistView.vue'),
      meta: { layout: 'AuthLayout', requiresGuest: true },
    }
  ],
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  
  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    if (authStore.role === 'Admin') {
      next('/admin');
    } else if (authStore.role === 'Artist') {
      next('/artist');
    } else {
      next('/');
    }
  } else {
    next();
  }
});

export default router
