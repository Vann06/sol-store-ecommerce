<template>
    <div class="ping-test">
      <h2>Health‑check Ping/Pong</h2>
      <div v-if="loading">⏳ Probando conexión…</div>
      <div v-else-if="error" class="error">❌ Error: {{ error }}</div>
      <div v-else class="success">✅ Respuesta: <code>{{ message }}</code></div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import axios from 'axios'
  
  const loading = ref(true)
  const message = ref('')
  const error = ref('')
  
  onMounted(async () => {
    try {
      const res = await axios.get('/api/ping')
      message.value = res.data.message || res.data
    } catch (err) {
      error.value = err.response?.data?.message || err.message
    } finally {
      loading.value = false
    }
  })
  </script>
  
  <style scoped>
  .ping-test {
    font-family: sans-serif;
    margin: 1em;
  }
  .error { color: #c00; }
  .success { color: #080; }
  </style>
  