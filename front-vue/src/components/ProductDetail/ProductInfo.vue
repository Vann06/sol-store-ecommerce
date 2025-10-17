<template>
  <div class="detalles-producto">
    <h1>{{ product.name }}</h1>
    <div class="precio">Q{{ product.price.toFixed(2) }}</div>

    <div class="seleccion-cantidad">
      <h3>CANTIDAD</h3>
      <div class="control-cantidad">
        <button class="boton-cantidad" @click="$emit('decrease')">-</button>
        <span class="cantidad">{{ cantidad }}</span>
        <button class="boton-cantidad" @click="$emit('increase')">+</button>
      </div>
    </div>

    <button class="boton-agregar-carrito" @click="$emit('add')">Añadir al carrito</button>
    <div class="info-envio">ENVÍO GRATIS EN PEDIDOS SUPERIORES A Q100.*</div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'
const props = defineProps({ product: { type: Object, required: true }, cantidad: { type: Number, required: true } })
</script>

<style scoped>
.detalles-producto { flex: 1 }
.detalles-producto h1 { font-size:2rem; margin-bottom:10px; color:#333 }
.precio { font-size:1.5rem; font-weight:bold; margin-bottom:30px; color:#780116 }
.control-cantidad { display:flex; align-items:center; margin-top:10px }
.boton-cantidad { padding:5px 15px; background:#f5f5f5; border:1px solid #ddd; cursor:pointer }
.cantidad { padding:0 15px }
.boton-agregar-carrito { background:#780116; color:white; border:none; padding:15px 40px; font-size:1rem; cursor:pointer; margin-top:20px; width:100%; border-radius:4px }
.info-envio { margin-top:10px; font-size:0.9rem; color:#666 }
</style>
