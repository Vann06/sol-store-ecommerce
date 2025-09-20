<template>
  <div class="page-header">
    <h2>
      <i :class="icon" v-if="icon"></i>
      {{ title }}
    </h2>
    <p class="page-subtitle" v-if="subtitle">{{ subtitle }}</p>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    default: ''
  }
})
</script>

<style scoped>
.page-header {
  margin-bottom: 30px;
  text-align: center;
}

.page-header h2 {
  color: #333;
  font-size: 28px;
  font-weight: 600;
  margin-bottom: 8px;
}

.page-header h2 i {
  color: #7d1c2b;
  margin-right: 12px;
}

.page-subtitle {
  color: #666;
  font-size: 16px;
  margin: 0;
}
</style>
