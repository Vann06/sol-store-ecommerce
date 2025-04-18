
import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from '@/stores/user'
import HomeView from '../views/HomeView.vue'
import LoginView from '@/views/LoginView.vue'
import SignUpView from '@/views/SignUpView.vue'
import Dashboard from '@/views/DashboardView.vue'


const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path:'/account/login',
    name: 'login',
    component: LoginView
  },
  {
    path:'/account/create',
    name: 'signup',
    component: SignUpView
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  }  
  // Agregá más rutas aquí
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const store = useUserStore()

  // si necesita auth
  if (to.meta.requiresAuth && !store.user) {
    return next('/account/login')
  }

  next()
})

export default router
