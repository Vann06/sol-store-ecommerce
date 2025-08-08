<template>
  <div class="page-header">
    <div class="header-content">
      <div class="header-text">
        <h1 class="page-title">
          <i v-if="icon" :class="icon" class="title-icon"></i>
          {{ title }}
        </h1>
        <p v-if="subtitle" class="page-subtitle">{{ subtitle }}</p>
      </div>
      
      <!-- Slot para contenido adicional (botones, breadcrumbs, etc.) -->
      <div v-if="$slots.actions" class="header-actions">
        <slot name="actions"></slot>
      </div>
    </div>
    
    <!-- Breadcrumbs opcionales -->
    <nav v-if="breadcrumbs && breadcrumbs.length" class="breadcrumbs">
      <ol class="breadcrumb-list">
        <li 
          v-for="(crumb, index) in breadcrumbs" 
          :key="index" 
          class="breadcrumb-item"
          :class="{ active: crumb.active }"
        >
          <RouterLink v-if="crumb.to && !crumb.active" :to="crumb.to" class="breadcrumb-link">
            {{ crumb.name }}
          </RouterLink>
          <span v-else class="breadcrumb-text">{{ crumb.name }}</span>
          <i v-if="index < breadcrumbs.length - 1" class="fas fa-chevron-right breadcrumb-separator"></i>
        </li>
      </ol>
    </nav>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    default: ''
  },
  breadcrumbs: {
    type: Array,
    default: () => []
  },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'compact', 'centered'].includes(value)
  }
})
</script>

<style scoped>
.page-header {
  margin-bottom: 2rem;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
  margin-bottom: 1rem;
}

.header-text {
  flex: 1;
}

.page-title {
  font-size: 2rem;
  font-weight: 700;
  color: #7d1c2b;
  margin: 0 0 0.5rem 0;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  line-height: 1.2;
}

.title-icon {
  color: #e5bf60;
  font-size: 1.8rem;
}

.page-subtitle {
  font-size: 1rem;
  color: #6c757d;
  margin: 0;
  font-weight: 400;
  line-height: 1.4;
}

.header-actions {
  flex-shrink: 0;
}

/* Breadcrumbs */
.breadcrumbs {
  padding: 0.75rem 0;
  border-top: 1px solid #e9ecef;
}

.breadcrumb-list {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin: 0;
  padding: 0;
  list-style: none;
}

.breadcrumb-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.breadcrumb-link {
  color: #7d1c2b;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s ease;
}

.breadcrumb-link:hover {
  color: #a27345;
  text-decoration: underline;
}

.breadcrumb-text {
  color: #6c757d;
  font-weight: 500;
}

.breadcrumb-item.active .breadcrumb-text {
  color: #7d1c2b;
  font-weight: 600;
}

.breadcrumb-separator {
  color: #adb5bd;
  font-size: 0.75rem;
}

/* Variantes */
.page-header.compact .page-title {
  font-size: 1.5rem;
}

.page-header.compact .page-subtitle {
  font-size: 0.875rem;
}

.page-header.centered .header-content {
  flex-direction: column;
  text-align: center;
}

.page-header.centered .header-actions {
  margin-top: 1rem;
}

/* Responsive */
@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    align-items: stretch;
  }
  
  .page-title {
    font-size: 1.75rem;
  }
  
  .page-subtitle {
    font-size: 0.875rem;
  }
  
  .header-actions {
    margin-top: 1rem;
  }
  
  .breadcrumb-list {
    flex-wrap: wrap;
    gap: 0.25rem 0.5rem;
  }
}

@media (max-width: 480px) {
  .page-title {
    font-size: 1.5rem;
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .title-icon {
    font-size: 1.5rem;
  }
}
</style>
