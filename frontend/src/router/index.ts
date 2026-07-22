import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/authStore'
import HomeView from '../views/HomeView.vue'
import AdminLayout from '../views/admin/AdminLayout.vue'

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
    },
    {
      path: '/forgot-password',
      name: 'forgot-password',
      component: () => import('../views/auth/ForgotPasswordView.vue'),
      meta: { layout: 'AuthLayout', requiresGuest: true },
    },
    {
      path: '/reset-password',
      name: 'reset-password',
      component: () => import('../views/auth/ResetPasswordView.vue'),
      meta: { layout: 'AuthLayout', requiresGuest: true },
    },
    {
      path: '/verify-email/:id/:hash',
      name: 'verify-email-process',
      component: () => import('../views/auth/VerifyEmailView.vue'),
      meta: { layout: 'AuthLayout' },
    },
    {
      path: '/awaiting-verification',
      name: 'awaiting-verification',
      component: () => import('../views/auth/AwaitingVerificationView.vue'),
      meta: { layout: 'AuthLayout', requiresAuth: true },
    },
    {
      path: '/settings',
      name: 'settings',
      component: () => import('../views/settings/SettingsLayout.vue'),
      meta: { requiresAuth: true },
      redirect: '/settings/profile',
      children: [
        {
          path: 'profile',
          name: 'settings-profile',
          component: () => import('../views/settings/ProfileSettingsView.vue'),
        },
        {
          path: 'security',
          name: 'settings-security',
          component: () => import('../views/settings/SecuritySettingsView.vue'),
        }
      ]
    },
    {
      path: '/admin',
      name: 'admin',
      component: AdminLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'admin-dashboard',
          component: () => import('../views/admin/AdminDashboardView.vue'),
        },
        {
          path: 'users',
          name: 'admin-users',
          component: () => import('../views/admin/users/UsersView.vue'),
        },
        {
          path: 'invites',
          name: 'admin-invites',
          component: () => import('../views/admin/invites/ArtistInvitesManagement.vue'),
        },
        {
          path: 'audit-logs',
          name: 'admin.audit-logs',
          component: () => import('../views/admin/audit/AuditLogsView.vue'),
        },
        {
          path: 'genres',
          name: 'admin-genres',
          component: () => import('../views/admin/genres/GenresView.vue'),
        },
        {
          path: 'albums',
          name: 'admin-albums',
          component: () => import('../views/admin/inventory/InventoryView.vue'), // Dùng chung view Inventory
        },
        {
          path: 'playlists',
          name: 'admin-playlists',
          component: () => import('../views/admin/playlists/PlaylistView.vue'),
        },
        {
          path: 'inventory',
          name: 'admin-inventory',
          component: () => import('../views/admin/inventory/InventoryView.vue'),
        },
        {
          path: 'moderation/songs',
          name: 'admin-song-moderation',
          component: () => import('../views/admin/moderation/ModerationView.vue'),
        },
        {
          path: 'banners',
          name: 'admin-banners',
          component: () => import('../views/admin/banners/BannerView.vue'),
        },
        {
          path: 'permissions',
          name: 'admin-permissions',
          component: () => import('../views/admin/roles/RolesManagement.vue'),
        }
      ]
    }
  ],
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  
  if (!authStore.isAuthenticated) {
    const savedRole = localStorage.getItem('app-role');
    if (savedRole) {
      try {
        await authStore.fetchProfile(savedRole);
      } catch (e) {
        // failed to restore session, clearAuth handles localStorage removal
      }
    }
  }

  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    const role = authStore.role?.toLowerCase();
    if (role === 'admin') {
      next('/admin');
    } else if (role === 'artist') {
      next('/artist');
    } else {
      next('/');
    }
  } else if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login');
  } else {
    next();
  }
});

export default router
