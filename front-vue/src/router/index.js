// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '@/views/LoginView.vue'
import SignUpView from '@/views/SignUpView.vue'

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
  }
  // Agregá más rutas aquí
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
