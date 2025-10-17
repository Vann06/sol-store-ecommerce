<template>
  <div class="wave-divider" :class="{ flip }" :style="dividerStyle" role="presentation" aria-hidden="true">
    <svg class="wave-svg" viewBox="0 0 1440 120" preserveAspectRatio="none">
      <defs>
        <linearGradient :id="gradId" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" :stop-color="colorTop"/>
          <stop offset="100%" :stop-color="colorBottom"/>
        </linearGradient>
      </defs>
      <path :fill="`url(#${gradId})`"
        d="M0,64 C240,96 480,0 720,24 C960,48 1200,144 1440,88 L1440,120 L0,120 Z"
      />
    </svg>
  </div>
  
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  colorTop: { type: String, default: '#ffffff' },
  colorBottom: { type: String, default: '#ffffff' },
  flip: { type: Boolean, default: false },
  id: { type: String, default: '' },
  overlap: { type: Number, default: 0 }
})

const gradId = computed(() => props.id || `wave-grad-${Math.random().toString(36).slice(2, 9)}`)

const dividerStyle = computed(() => ({
  marginTop: props.overlap ? `-${props.overlap}px` : undefined,
  pointerEvents: 'none',
  position: 'relative',
  zIndex: 1
}))
</script>

<style scoped>
.wave-divider {
  position: relative;
  width: 100vw;
  margin-left: calc(50% - 50vw);
  margin-right: calc(50% - 50vw);
  line-height: 0;
}
.wave-divider.flip .wave-svg {
  transform: rotate(180deg);
}
.wave-svg {
  display: block;
  width: 100%;
  height: 120px;
}
</style>
