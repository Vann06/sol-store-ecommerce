<template>
  <span class="ui-badge" :class="[`tone-${tone}`, { pill }]">
    <slot />
  </span>
</template>

<script setup>
defineProps({
  tone: { type: String, default: 'brand', validator: v => ['brand','success','warning','danger','neutral'].includes(v) },
  pill: { type: Boolean, default: true }
})
</script>

<style scoped>
.ui-badge { display: inline-flex; align-items: center; gap: 6px; font-weight: 800; font-size: 12px; padding: 4px 10px; }
.pill { border-radius: 999px; }

.tone-brand { color: #fff; background: linear-gradient(135deg, var(--brand-strong), var(--brand)); }
.tone-success { color:#fff; background: linear-gradient(135deg,#22c55e,#16a34a); }
.tone-warning { color:#111; background: linear-gradient(135deg,#fde047,#f59e0b); }
.tone-danger { color:#fff; background: linear-gradient(135deg,#ef4444,#dc2626); }
.tone-neutral { color:#111; background: #e5e7eb; }
</style>