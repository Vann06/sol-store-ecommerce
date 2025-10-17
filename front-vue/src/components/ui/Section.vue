<template>
  <section
    class="ui-section"
    :class="[
      `variant-${variant}`,
      { 'is-contained': contained, 'no-padding': !padded }
    ]"
  >
    <div v-if="contained" class="section-inner">
      <slot />
    </div>
    <slot v-else />
  </section>
</template>

<script setup>
defineProps({
  variant: { type: String, default: 'surface', validator: v => ['surface','transparent','soft','brand','outline'].includes(v) },
  padded: { type: Boolean, default: true },
  contained: { type: Boolean, default: true }
})
</script>

<style scoped>
.ui-section { padding: 32px 16px; }
.ui-section.no-padding { padding: 0; }

.section-inner { max-width: 1200px; margin: 0 auto; }

/* Variants */
.variant-surface { background: var(--surface); }
.variant-transparent { background: transparent; }
.variant-soft { background: var(--surface-2); }
.variant-brand { color: #fff; background: linear-gradient(135deg, var(--brand), var(--brand-2)); }
.variant-outline { background: linear-gradient(var(--surface), var(--surface)) padding-box,
                              linear-gradient(135deg, rgba(240,192,64,0.35), rgba(122,0,25,0.3)) border-box;
                   border: 1px solid transparent; border-radius: 16px; }

/* Spacing helpers responsive */
@media (max-width: 768px) {
  .ui-section { padding: 24px 12px; }
}
</style>