<template>
  <div class="section-header" :class="[`align-${align}`]">
    <div class="header-text">
      <h2 class="title">
        <i v-if="icon" :class="icon" class="title-icon"></i>
        {{ title }}
      </h2>
      <p v-if="subtitle" class="subtitle">{{ subtitle }}</p>
    </div>
    <div v-if="$slots.actions" class="header-actions">
      <slot name="actions" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: { type: String, required: true },
  subtitle: { type: String, default: '' },
  icon: { type: String, default: '' },
  align: { type: String, default: 'left', validator: v => ['left','center'].includes(v) }
})
</script>

<style scoped>
.section-header { display: flex; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
.section-header.align-center { flex-direction: column; align-items: center; text-align: center; }
.header-text { flex: 1; }
.title { margin: 0 0 6px; color: var(--ink-1); font-weight: 800; font-size: 24px; }
.title-icon { color: var(--accent); margin-right: 8px; }
.subtitle { margin: 0; color: var(--ink-3); }
.header-actions { flex-shrink: 0; display: inline-flex; gap: 8px; }

@media (max-width: 640px) {
  .title { font-size: 20px; }
}
</style>