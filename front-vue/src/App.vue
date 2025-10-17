<template>
  <div class="app-wrapper">
    <Header />
    <div class="app-content">
      <router-view />
    </div>
    <Footer />
  </div>
</template>

<script setup>
import Header from '@/components/Header.vue'
import Footer from '@/components/Footer.vue'
import { useAppInit } from '@/composables/useAppInit'
import { useClarity } from '@/composables/useClarity'
import { watch } from 'vue'
import { useRoute } from 'vue-router'

// Inicializar la aplicación (verifica token, configura axios, etc.)
useAppInit()

// Inicializar Microsoft Clarity
const { trackEcommerce } = useClarity()

// Tracking automático de cambios de página
const route = useRoute()
watch(
  () => route.path,
  (newPath, oldPath) => {
    if (newPath !== oldPath) {
      // Track page view con un pequeño delay para asegurar que la página se haya cargado
      setTimeout(() => {
        const pageName = route.name || route.path
        const pageCategory = route.path.split('/')[1] || 'home'
        trackEcommerce.pageView(pageName, pageCategory)
      }, 100)
    }
  },
  { immediate: true }
)

</script>

<style>
html, body, #app {
  height: 100%;
  margin: 0;
  padding: 0;
  font-family: 'Poppins', sans-serif;
}

.app-wrapper {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  width: 100vw;
  overflow-x: hidden;
}

.app-content {
  flex: 1;
  display: flex;
  flex-direction: column;
}

body {
  background-color: white !important;
}

</style>
