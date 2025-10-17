<template>
  <PageHeader v-bind="$attrs">
    <template v-for="(_, name) in $slots" #[name]="slotProps">
      <slot :name="name" v-bind="slotProps" />
    </template>
  </PageHeader>
  <!-- Wrapper to keep existing Account imports working while unifying header style. -->
</template>

<script setup>
import PageHeader from '@/components/PageHeader.vue'
</script>
