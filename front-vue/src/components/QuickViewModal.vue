<template>
  <teleport to="body">
    <div v-if="visible" class="qv-overlay" @click.self="close">
      <div class="qv-modal gradient-outline">
        <button class="qv-close" @click="close" aria-label="Cerrar vista rápida">×</button>

        <div class="qv-content">
          <div class="qv-media">
            <img :src="product.imagen || '/img/no-image.png'" :alt="product.nombre" />
          </div>
          <div class="qv-info">
            <h3 class="qv-title">{{ product.nombre }}</h3>
            <div class="qv-price">{{ format(product.precio) }}</div>
            <p v-if="product.descripcion" class="qv-desc">{{ product.descripcion }}</p>

            <div class="qv-actions">
              <BaseButton variant="primary" size="large" block @click="add">Añadir al carrito</BaseButton>
              <BaseButton variant="outline-primary" size="large" block @click="close">Cerrar</BaseButton>
            </div>
          </div>
        </div>
      </div>
    </div>
  </teleport>
</template>

<script setup>
import BaseButton from '@/components/BaseButton.vue'

const props = defineProps({
  product: { type: Object, required: true },
  visible: { type: Boolean, default: false }
})

const emit = defineEmits(['close', 'add-to-cart'])

const format = (n) => Number(n || 0).toLocaleString('es-GT',{style:'currency',currency:'GTQ',minimumFractionDigits:2,maximumFractionDigits:2})

const close = () => emit('close')
const add = () => emit('add-to-cart', { product: props.product, quantity: 1 })
</script>

<style scoped>
.qv-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: grid; place-items: center; z-index: 60; padding: 16px; }
.qv-modal { max-width: 960px; width: 100%; background: #fff; border-radius: 16px; overflow: hidden; position: relative; box-shadow: 0 20px 60px rgba(0,0,0,0.25); }
.qv-close { position: absolute; top: 8px; right: 10px; width: 36px; height: 36px; border-radius: 999px; border: 0; background: var(--surface); box-shadow: 0 2px 8px rgba(0,0,0,0.12); cursor: pointer; font-size: 20px; }
.qv-content { display: grid; grid-template-columns: 1fr 1fr; gap: 0; }
.qv-media { background: #fff; display: grid; place-items: center; padding: 16px; }
.qv-media img { width: 100%; height: 100%; max-height: 420px; object-fit: contain; }
.qv-info { padding: 16px 16px 20px; display: grid; gap: 10px; align-content: start; }
.qv-title { margin: 0; color: var(--ink-1); font-size: 22px; }
.qv-price { color: var(--brand-strong); font-weight: 800; font-size: 20px; }
.qv-desc { color: var(--ink-3); font-size: 14px; margin: 4px 0 10px; max-height: 120px; overflow: auto; }
.qv-actions { display: grid; gap: 8px; }

@media (max-width: 720px) {
  .qv-content { grid-template-columns: 1fr; }
}
</style>
