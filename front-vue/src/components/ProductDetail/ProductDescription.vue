<template>
  <div class="descripcion-producto">
    <h2>Detalles</h2>
    <p v-if="product.descripcion" class="descripcion-texto">{{ product.descripcion }}</p>

    <div class="detalles-grid">
      <div class="detalle-item">
        <span class="detalle-label">Categoría</span>
        <span class="detalle-value">{{ product.category || '—' }}</span>
      </div>
      <div class="detalle-item">
        <span class="detalle-label">Temática</span>
        <span class="detalle-value">{{ product.theme || '—' }}</span>
      </div>
      <div class="detalle-item">
        <span class="detalle-label">Precio base</span>
        <span class="detalle-value precio">{{ Number(product.price).toFixed(2) }}</span>
      </div>
      <div class="detalle-item">
        <span class="detalle-label">Stock</span>
        <span class="detalle-value">
          <span :class="['badge', product.stock > 0 ? 'badge--ok' : 'badge--out']">
            {{ product.stock > 0 ? `Disponible (${product.stock})` : 'Sin stock' }}
          </span>
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'
const props = defineProps({ product: { type: Object, required: true } })
</script>

<style scoped>
.descripcion-producto { margin:50px 0; padding-top:30px; border-top:1px solid #eee }
.descripcion-producto h2 { margin-bottom:20px; color:#333 }
.descripcion-texto { margin-bottom:18px; line-height:1.6; color:#555 }
.detalles-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:12px }
.detalle-item { display:flex; flex-direction:column; background:#fff; border:1px solid #eee; border-radius:10px; padding:12px 14px }
.detalle-label { font-size:12px; color:#777; letter-spacing:.3px }
.detalle-value { font-size:15px; color:#2b2b2b; margin-top:2px }
.precio { color:#780116; font-weight:600 }
.badge { display:inline-block; padding:4px 10px; border-radius:999px; font-size:12px; border:1px solid transparent }
.badge--ok { background:#f0fff5; color:#137333; border-color:#ccebd7 }
.badge--out { background:#fff5f5; color:#a31d1d; border-color:#f1cdcd }

@media (max-width:768px) { .contenedor-producto { flex-direction: column; gap: 30px } }
</style>
