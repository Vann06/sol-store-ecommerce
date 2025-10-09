import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import http from './http'
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import clarityPlugin from './plugins/clarity'
import './assets/fixed-header.css' // utilidades .under-fixed-header y CSS var

// El cliente HTTP (http.js) ya maneja la configuración de axios automáticamente
// incluyendo baseURL, withCredentials e interceptores para tokens

// Crear instancia de Vue y Pinia correctamente
const app = createApp(App)
const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)

app.use(pinia)
app.use(router)
app.use(clarityPlugin)

// Inicializar store de usuario después de configurar Pinia
import { useUserStore } from '@/stores/userStore'
const userStore = useUserStore()
userStore.initialize()

app.mount('#app')
