import './assets/main.css'
import './assets/css/bootstrap.min.css'
import './assets/css/font-awesome.min.css'
import './assets/css/elegant-icons.css'
import './assets/css/owl.carousel.min.css'
import './assets/css/slicknav.min.css'
import './assets/css/style.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'

const app = createApp(App)

const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)

app.use(createPinia())
app.use(router)
app.use(pinia)

app.mount('#app')
