<template>
  <main class="page under-fixed-header">
    <div class="container">
      <h1>Detalle de pedido</h1>
      <p>Pedido #{{ $route.params.id }}</p>
      <RouterLink to="/account/orders">Volver a pedidos</RouterLink>
    </div>
  </main>
</template>

<script setup>
</script>

<style scoped>
.container { max-width: 960px; margin: 0 auto; padding: calc(var(--header-height) + 24px) 20px 20px; }
</style>
