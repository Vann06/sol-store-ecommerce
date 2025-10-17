<template>
  <Transition name="popup-fade" appear>
    <div v-if="visible" class="popup-overlay" role="dialog" aria-modal="true">
      <div class="popup-card gradient-card">
        <button class="close-btn" @click="$emit('close')" aria-label="Cerrar">
          <i class="fas fa-times"></i>
        </button>
        <div class="popup-body">
          <div class="icon-wrap">
            <i :class="icon"></i>
          </div>
          <h3 class="title">{{ title }}</h3>
          <p v-if="message" class="message">{{ message }}</p>
          <div class="actions">
            <BaseButton variant="primary" @click="$emit('primary')">{{ primaryText }}</BaseButton>
            <BaseButton variant="ghost" @click="$emit('close')">{{ secondaryText }}</BaseButton>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import BaseButton from '@/components/BaseButton.vue'

defineProps({
  visible: { type: Boolean, default: false },
  title: { type: String, default: '¡Listo!' },
  message: { type: String, default: '' },
  icon: { type: String, default: 'fas fa-check-circle' },
  primaryText: { type: String, default: 'Ver carrito' },
  secondaryText: { type: String, default: 'Seguir comprando' }
})
</script>

<style scoped>
.popup-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: grid; place-items: center; z-index: 1000; }
.popup-card { position: relative; width: min(92vw, 520px); background: #fff; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.25); overflow: hidden; }
.gradient-card { background: linear-gradient(#fff, #fff) padding-box,
                 linear-gradient(135deg, rgba(240,192,64,0.35), rgba(122,0,25,0.3)) border-box;
                 border: 1px solid transparent; }
.close-btn { position: absolute; top: 10px; right: 10px; border: 0; background: transparent; cursor: pointer; font-size: 18px; opacity: .7; }
.close-btn:hover { opacity: 1; }
.popup-body { padding: 28px 22px; text-align: center; }
.icon-wrap { font-size: 44px; color: var(--brand-strong); margin-bottom: 8px; text-shadow: 2px 2px 0 rgba(0,0,0,0.06); }
.title { margin: 8px 0 6px; font-size: 22px; color: var(--ink-1); }
.message { margin: 0 0 16px; color: var(--ink-2); }
.actions { display: flex; gap: 10px; justify-content: center; }

/* Anim */
.popup-fade-enter-active, .popup-fade-leave-active { transition: opacity .25s ease, transform .25s ease; }
.popup-fade-enter-from, .popup-fade-leave-to { opacity: 0; transform: scale(0.95); }
</style>
