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
