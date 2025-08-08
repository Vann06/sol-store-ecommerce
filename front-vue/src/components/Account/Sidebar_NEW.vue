<template>
  <aside class="sidebar">
    <!-- Header del usuario -->
    <div class="sidebar-header">
      <h3>Mi Cuenta</h3>
      <UserCard :user="user" />
    </div>
    
    <!-- Navegación -->
    <nav class="sidebar-nav">
      <NavItem 
        v-for="item in navigationItems"
        :key="item.to"
        :to="item.to"
        :icon="item.icon"
        :title="item.title"
        :subtitle="item.subtitle"
        :badge="item.badge"
      />
      
      <!-- Separador -->
      <div class="nav-separator"></div>
      
      <!-- Botón de logout -->
      <button @click="handleLogout" class="nav-link logout-btn">
        <div class="nav-icon">
          <i class="fas fa-sign-out-alt"></i>
        </div>
        <div class="nav-text">
          <span class="nav-title">Cerrar Sesión</span>
          <span class="nav-subtitle">Salir de mi cuenta</span>
        </div>
      </button>
    </nav>
  </aside>
</template>

<script setup>
import { computed } from 'vue'
import { useUserStore } from '@/stores/userStore'
import { useLogout } from '@/composables/useLogout'
import UserCard from './UserCard.vue'
import NavItem from './NavItem.vue'

const userStore = useUserStore()
const { logout } = useLogout()

// Datos computados
const user = computed(() => userStore.user)

// Elementos de navegación configurables
const navigationItems = [
  {
    to: '/account/orders',
    icon: 'fas fa-shopping-bag',
    title: 'Mis Pedidos',
    subtitle: 'Ver historial de compras',
    badge: computed(() => userStore.pendingOrders)
  },
  {
    to: '/account/address',
    icon: 'fas fa-map-marker-alt',
    title: 'Direcciones',
    subtitle: 'Gestionar direcciones de envío'
  },
  {
    to: '/account/details',
    icon: 'fas fa-user-edit',
    title: 'Datos Personales',
    subtitle: 'Actualizar información'
  },
  {
    to: '/account/password',
    icon: 'fas fa-lock',
    title: 'Cambiar Contraseña',
    subtitle: 'Actualizar credenciales'
  }
]

// Métodos
const handleLogout = async () => {
  try {
    await logout()
  } catch (error) {
    console.error('Error during logout:', error)
  }
}
</script>

<style scoped>
.sidebar {
  width: 300px;
  background: linear-gradient(145deg, #7d1c2b 0%, #a27345 100%);
  color: white;
  display: flex;
  flex-direction: column;
  box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
}

.sidebar-header {
  padding: 2rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-header h3 {
  margin: 0 0 1.5rem 0;
  font-size: 1.5rem;
  font-weight: 700;
  text-align: center;
  color: #e5bf60;
}

.sidebar-nav {
  flex: 1;
  padding: 1rem 0;
}

.nav-separator {
  height: 1px;
  background: rgba(255, 255, 255, 0.1);
  margin: 1rem 2rem;
}

.logout-btn {
  background: none;
  border: none;
  width: 100%;
  text-align: left;
  color: inherit;
  font: inherit;
  cursor: pointer;
  transition: all 0.3s ease;
}

.logout-btn:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #e5bf60;
}

/* Responsive */
@media (max-width: 768px) {
  .sidebar {
    width: 100%;
    flex-direction: row;
    overflow-x: auto;
  }
  
  .sidebar-header {
    min-width: 250px;
  }
  
  .sidebar-nav {
    flex-direction: row;
    min-width: max-content;
  }
}
</style>
