// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  // Agregá más rutas aquí
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
