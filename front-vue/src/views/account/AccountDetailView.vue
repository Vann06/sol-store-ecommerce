<template>
  <div>
    <h2>Account Details</h2>
    <form v-if="user">
      <input type="text" v-model="user.first_name" placeholder="First Name" />
      <input type="email" v-model="user.email" placeholder="Email" />
      <button>Save Changes</button>
    </form>
    <p v-else>Loading user...</p>
  </div>
</template>

<script setup>
import { useUserStore } from '@/stores/userStore'
import { computed } from 'vue'

const userStore = useUserStore()
const user = computed(() => userStore.getUser)
</script>
