// vite.config.js
import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

export default defineConfig({
  plugins: [ vue(), vueDevTools() ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
  server: {
    allowedHosts: [
	          'solstoredev.duckdns.org',
	          ],
    host: true,      // o '0.0.0.0'
    port: 5173,
    strictPort: true,
    proxy: {
      '/api': {
        target: 'http://backend',  // apunta al servicio Docker "backend"
        changeOrigin: true,
        secure: false,
      }
    }
  },
	preview: {
		    host: true,
		    allowedHosts: ['solstoredev.duckdns.org'],
		  },
})
