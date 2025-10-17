<template>
  <div class="galeria-producto">
    <div class="imagen-principal">
      <img :src="image" :alt="alt" />
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'
const props = defineProps({ image: { type: String, required: true }, alt: { type: String, default: '' } })
</script>

<style scoped>
.galeria-producto { flex: 1 }
.imagen-principal img { width:100%; height:auto; border-radius:8px }
</style>
