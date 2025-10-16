<template>
  <div class="ui-card" :class="[`type-${type}`, { hover }]">
    <slot />
  </div>
</template>

<script setup>
defineProps({
  type: { type: String, default: 'outlined', validator: v => ['outlined','filled','elevated'].includes(v) },
  hover: { type: Boolean, default: true }
})
</script>

<style scoped>
.ui-card { border-radius: 16px; overflow: hidden; transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease; }
.type-outlined { background: linear-gradient(#fff, #fff) padding-box,
                            linear-gradient(135deg, rgba(240,192,64,0.45), rgba(122,0,25,0.35)) border-box;
                 border: 1px solid transparent; }
.type-filled { background: var(--surface); border: 1px solid rgba(122,0,25,0.08); }
.type-elevated { background: var(--surface); box-shadow: 0 12px 30px rgba(31,41,55,0.12); border: 1px solid rgba(122,0,25,0.08); }
.hover:hover { transform: translateY(-4px); box-shadow: 0 18px 36px rgba(122,0,25,0.18), 0 2px 6px rgba(0,0,0,0.06); }
</style>