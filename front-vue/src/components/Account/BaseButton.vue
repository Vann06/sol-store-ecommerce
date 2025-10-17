<!-- Wrapper to reuse the global BaseButton while preserving existing import paths -->
<template>
	<BaseButton v-bind="$attrs">
		<slot />
	</BaseButton>
	<!-- Note: All styling and variants come from the global component -->
	<!-- This wrapper prevents duplication and keeps Account screens consistent. -->
	<!-- If you need to pass listeners like @click, they're forwarded via $attrs. -->
	<!-- Keep this file to avoid breaking imports in existing views. -->
  
	<!-- If you need scoped overrides for Account only, consider using classes on the parent instead. -->
</template>

<script setup>
import BaseButton from '@/components/BaseButton.vue'
</script>
