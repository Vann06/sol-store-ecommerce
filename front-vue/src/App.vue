<script setup>
import HelloWorld  from './components/HelloWorld.vue'
import TaskList    from './components/TaskList.vue'
import TheWelcome  from './components/TheWelcome.vue'
import PingTest    from './components/PingTest.vue'    // ← IMPORTA tu health‑check
</script>

<template>
  <header>
    <img
      alt="Vue logo"
      class="logo"
      src="./assets/logo.svg"
      width="125"
      height="125"
    />
    <div class="wrapper">
      <HelloWorld msg="ToDo App" />
    </div>
  </header>

  <main>
    <!-- ← AÑADE tu componente aquí -->
    <PingTest />

    <!-- Luego tu lista de tareas -->
    <TaskList />

    <!-- <TheWelcome /> -->
  </main>
</template>

<style scoped>
/* ... tu CSS ... */
</style>

