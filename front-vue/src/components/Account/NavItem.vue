<template>
  <RouterLink 
    :to="to" 
    class="nav-link" 
    active-class="active"
    @click="handleClick"
  >
    <div class="nav-icon">
      <i :class="icon"></i>
      <span v-if="badge && badge > 0" class="nav-badge">{{ badge }}</span>
    </div>
    <div class="nav-text">
      <span class="nav-title">{{ title }}</span>
      <span class="nav-subtitle" v-if="subtitle">{{ subtitle }}</span>
    </div>
    <div class="nav-arrow" v-if="showArrow">
      <i class="fas fa-chevron-right"></i>
    </div>
  </RouterLink>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  to: {
    type: String,
    required: true
  },
  icon: {
    type: String,
    required: true
  },
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  badge: {
    type: [Number, String],
    default: null
  },
  showArrow: {
    type: Boolean,
    default: true
  },
  disabled: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['click'])

// Computed
const badgeValue = computed(() => {
  if (typeof props.badge === 'function') {
    return props.badge()
  }
  return props.badge
})

// Methods
const handleClick = (event) => {
  if (props.disabled) {
    event.preventDefault()
    return
  }
  emit('click', event)
}
</script>

<style scoped>
.nav-link {
  display: flex;
  align-items: center;
  padding: 1rem 2rem;
  color: rgba(255, 255, 255, 0.9);
  text-decoration: none;
  transition: all 0.3s ease;
  position: relative;
  border-left: 3px solid transparent;
}

.nav-link:hover {
  background: rgba(255, 255, 255, 0.1);
  color: white;
  border-left-color: #e5bf60;
}

.nav-link.active {
  background: rgba(229, 191, 96, 0.2);
  border-left-color: #e5bf60;
  color: white;
}

.nav-link.disabled {
  opacity: 0.5;
  cursor: not-allowed;
  pointer-events: none;
}

.nav-icon {
  position: relative;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1rem;
  flex-shrink: 0;
}

.nav-icon i {
  font-size: 1.1rem;
}

.nav-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background: #dc3545;
  color: white;
  border-radius: 10px;
  font-size: 0.7rem;
  font-weight: bold;
  padding: 2px 6px;
  min-width: 18px;
  height: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid #7d1c2b;
}

.nav-text {
  flex: 1;
  min-width: 0;
}

.nav-title {
  display: block;
  font-weight: 600;
  font-size: 0.95rem;
  margin-bottom: 0.1rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.nav-subtitle {
  display: block;
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.7);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.nav-arrow {
  margin-left: 0.5rem;
  opacity: 0.5;
  transition: all 0.3s ease;
}

.nav-link:hover .nav-arrow {
  opacity: 1;
  transform: translateX(2px);
}

.nav-link.active .nav-arrow {
  opacity: 1;
  color: #e5bf60;
}
</style>
