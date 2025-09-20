<template>
    <aside class="sidebar">
      <div class="sidebar-header">
        <h3>Mi Cuenta</h3>
        <div class="user-info" v-if="user">
          <div class="user-avatar">
            <i class="fa fa-user-circle"></i>
          </div>
          <div class="user-details">
            <p class="user-name">{{ user.first_name }} {{ user.last_name }}</p>
            <p class="user-email">{{ user.email }}</p>
          </div>
        </div>
      </div>
      
      <nav class="sidebar-nav">
        <RouterLink to="/account/orders" class="nav-link" active-class="active">
          <div class="nav-icon">
            <i class="fa fa-shopping-bag"></i>
          </div>
          <div class="nav-text">
            <span class="nav-title">Mis Pedidos</span>
            <span class="nav-subtitle">Ver historial de compras</span>
          </div>
        </RouterLink>
        
        <RouterLink to="/account/address" class="nav-link" active-class="active">
          <div class="nav-icon">
            <i class="fa fa-map-marker-alt"></i>
          </div>
          <div class="nav-text">
            <span class="nav-title">Direcciones</span>
            <span class="nav-subtitle">Gestionar direcciones de envío</span>
          </div>
        </RouterLink>
        
        <RouterLink to="/account/details" class="nav-link" active-class="active">
          <div class="nav-icon">
            <i class="fa fa-user-edit"></i>
          </div>
          <div class="nav-text">
            <span class="nav-title">Datos Personales</span>
            <span class="nav-subtitle">Actualizar información</span>
          </div>
        </RouterLink>
        
        <RouterLink to="/account/password" class="nav-link" active-class="active">
          <div class="nav-icon">
            <i class="fa fa-lock"></i>
          </div>
          <div class="nav-text">
            <span class="nav-title">Contraseña</span>
            <span class="nav-subtitle">Cambiar contraseña</span>
          </div>
        </RouterLink>
        
        <div class="nav-divider"></div>
        
        <button @click="handleLogout" class="nav-link logout-link logout-button">
          <div class="nav-icon">
            <i class="fa fa-sign-out-alt"></i>
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
  
  const userStore = useUserStore()
  const { logout } = useLogout()
  const user = computed(() => userStore.getUser)
  
  const handleLogout = async () => {
    await logout()
  }
  </script>
  
  <style scoped>
.sidebar {
  width: 300px;
  background: linear-gradient(135deg, #7d1c2b 0%, #a27345 100%);
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(125, 28, 43, 0.3);
}  .sidebar-header {
    padding: 25px 20px;
    background: rgba(255, 255, 255, 0.1);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
  }
  
  .sidebar-header h3 {
    color: white;
    margin: 0 0 20px 0;
    font-size: 24px;
    font-weight: 600;
    text-align: center;
  }
  
  .user-info {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  
  .user-avatar {
    color: white;
    font-size: 32px;
    opacity: 0.9;
  }
  
  .user-details {
    flex: 1;
  }
  
  .user-name {
    color: white;
    font-weight: 600;
    margin: 0 0 4px 0;
    font-size: 16px;
  }
  
  .user-email {
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
    font-size: 13px;
  }
  
  .sidebar-nav {
    padding: 20px 0;
  }
  
  .nav-link {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px 20px;
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
    position: relative;
  }
  
  .nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    transform: translateX(5px);
  }
  
.nav-link.active {
  background: rgba(229, 191, 96, 0.2);
  color: #e5bf60;
  border-left-color: #e5bf60;
  transform: translateX(5px);
}

.nav-link.active::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 4px;
  background: #e5bf60;
}  .nav-icon {
    width: 24px;
    text-align: center;
    font-size: 18px;
  }
  
  .nav-text {
    flex: 1;
  }
  
  .nav-title {
    display: block;
    font-weight: 600;
    font-size: 15px;
    margin-bottom: 2px;
  }
  
  .nav-subtitle {
    display: block;
    font-size: 12px;
    opacity: 0.7;
    line-height: 1.3;
  }
  
  .nav-divider {
    height: 1px;
    background: rgba(255, 255, 255, 0.2);
    margin: 15px 20px;
  }
  
  .logout-link {
    color: rgba(255, 255, 255, 0.8) !important;
  }
  
  .logout-button {
    background: none;
    border: none;
    font-family: inherit;
    font-size: inherit;
    text-align: left;
    width: 100%;
    cursor: pointer;
  }
  
.logout-link:hover {
  background: rgba(220, 53, 69, 0.2);
  color: #e5bf60 !important;
}  /* Responsive */
  @media (max-width: 768px) {
    .sidebar {
      width: 100%;
      margin-bottom: 20px;
    }
    
    .nav-link {
      padding: 12px 15px;
    }
    
    .nav-subtitle {
      display: none;
    }
  }
  </style>  