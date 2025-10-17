<!-- Wrapper to reuse global FormInput and keep Account imports working -->
<template>
  <FormInput v-bind="$attrs">
    <template v-for="(_, name) in $slots" #[name]="slotProps">
      <slot :name="name" v-bind="slotProps" />
    </template>
  </FormInput>
</template>

<script setup>
import FormInput from '@/components/FormInput.vue'
</script>
