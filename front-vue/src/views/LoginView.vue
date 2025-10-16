<template>
  <main class="login-page under-fixed-header bg-page-soft">
    <div class="auth-container gradient-outline">
      <div class="auth-info">
        <PageHeader title="Bienvenido" subtitle="Inicia sesión para continuar" icon="fas fa-user-circle" />
        <SignUpInfo />
      </div>
      <LoginForm />
    </div>
  </main>

</template>

<script setup>
import PageHeader from '@/components/PageHeader.vue'
import SignUpInfo from '@/components/SignUpInfo.vue'
import LoginForm from '@/components/LoginForm.vue'
</script>

<style scoped>
.login-page {
  /* Más aire debajo del header fijo y buen espacio inferior */
  min-height: 100vh;
  padding: 24px 0 48px;
}
.auth-container {
  /* Panel principal: borde con gradiente y proporción agradable */
  display: grid;
  grid-template-columns: 1.2fr 1fr;
  background-color: var(--surface);
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(31, 41, 55, 0.06);
  width: 100%;
  max-width: 1100px;
  min-height: 560px;
  overflow: hidden;
  margin: 0 auto;
}

.auth-info { padding: 32px; flex: 1; }

/* Asegurar que esta vista tenga un poco más de aire bajo el header */
.under-fixed-header { padding-top: calc(var(--header-height, 96px) + 16px); }

@media (max-width: 1024px) {
  .auth-container {
    grid-template-columns: 1fr;
    min-height: unset;
  }
  .auth-info { padding: 24px; }
}
</style>
