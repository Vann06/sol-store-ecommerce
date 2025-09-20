<template>
  <div class="user-card" v-if="user">
    <div class="user-avatar">
      <img 
        v-if="user.avatar" 
        :src="user.avatar" 
        :alt="`Avatar de ${user.first_name}`"
        class="avatar-image"
      />
      <i v-else class="fas fa-user-circle avatar-icon"></i>
    </div>
    <div class="user-details">
      <p class="user-name">{{ fullName }}</p>
      <p class="user-email">{{ user.email }}</p>
      <div class="user-status">
        <i class="fas fa-circle" :class="statusClass"></i>
        <span>{{ statusText }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  user: {
    type: Object,
    default: () => null
  }
})

// Computed properties
const fullName = computed(() => {
  if (!props.user) return ''
  return `${props.user.first_name || ''} ${props.user.last_name || ''}`.trim()
})

const statusClass = computed(() => {
  return props.user?.is_active ? 'status-online' : 'status-offline'
})

const statusText = computed(() => {
  return props.user?.is_active ? 'Activo' : 'Inactivo'
})
</script>

<style scoped>
.user-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border-radius: 12px;
  padding: 1rem;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.user-avatar {
  flex-shrink: 0;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(229, 191, 96, 0.2);
  border: 2px solid #e5bf60;
}

.avatar-image {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

.avatar-icon {
  font-size: 2rem;
  color: #e5bf60;
}

.user-details {
  flex: 1;
  min-width: 0; /* Para permitir text-overflow */
}

.user-name {
  font-weight: 600;
  font-size: 1rem;
  margin: 0 0 0.25rem 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  color: white;
}

.user-email {
  font-size: 0.875rem;
  margin: 0 0 0.5rem 0;
  color: rgba(255, 255, 255, 0.8);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-status {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.7);
}

.user-status i {
  font-size: 0.5rem;
}

.status-online {
  color: #28a745;
}

.status-offline {
  color: #6c757d;
}
</style>
