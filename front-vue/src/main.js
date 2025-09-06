import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import axios from 'axios'
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import clarityPlugin from './plugins/clarity'
import './assets/fixed-header.css' // utilidades .under-fixed-header y CSS var

// Axios setup
axios.defaults.baseURL = 'http://localhost:8000'
axios.defaults.withCredentials = true
const token = localStorage.getItem('auth_token')
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

// Crear instancia de Vue y Pinia correctamente
const app = createApp(App)
const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)

app.use(pinia)
app.use(router)
app.use(clarityPlugin)

app.mount('#app')
