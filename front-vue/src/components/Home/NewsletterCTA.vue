<template>
  <section class="newsletter">
    <div class="wrap">
      <div class="copy">
        <h3 class="title">Recibe novedades y ofertas</h3>
        <p class="sub">Únete a nuestra comunidad para enterarte de lanzamientos, descuentos y piezas exclusivas.</p>
      </div>
      <form class="form" @submit.prevent="submit">
        <input
          v-model="email"
          type="email"
          required
          class="input"
          placeholder="tu@email.com"
          aria-label="Correo electrónico"
        />
        <BaseButton type="submit" variant="primary" size="large">Suscribirme</BaseButton>
      </form>
      <p class="hint">Prometemos no hacer spam. Puedes darte de baja en cualquier momento.</p>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import BaseButton from '@/components/BaseButton.vue'

const email = ref('')
const submit = () => {
  // Placeholder – wire to backend or newsletter service later
  console.log('Newsletter subscribe:', email.value)
  email.value = ''
}
</script>

<style scoped>
.newsletter { background: linear-gradient(135deg, rgba(122,0,25,0.06), rgba(240,192,64,0.05)); border-radius: 16px; padding: 20px; }
.wrap { max-width: 900px; margin: 0 auto; text-align: center; display: grid; gap: 12px; }
.title { margin: 0; font-size: 24px; font-weight: 900; color: var(--ink-1); }
.sub { margin: 0; color: var(--ink-3); }
.form { display: grid; grid-template-columns: 1fr auto; gap: 10px; align-items: center; }
.input { appearance: none; border: 1px solid var(--ink-5); background: #fff; padding: 12px 14px; border-radius: 12px; font-size: 16px; outline: none; }
.input:focus { border-color: rgba(122,0,25,0.35); box-shadow: 0 0 0 3px rgba(122,0,25,0.12); }
.hint { margin: 0; color: var(--ink-4); font-size: 12px; }

@media (max-width: 640px) {
  .form { grid-template-columns: 1fr; }
}
</style>
