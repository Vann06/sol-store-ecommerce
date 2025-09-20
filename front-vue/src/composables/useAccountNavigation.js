import { computed } from 'vue'
import { useUserStore } from '@/stores/userStore'

/**
 * Composable para manejar la configuración de navegación de la cuenta
 * Centraliza la lógica de navegación y permite fácil mantenimiento
 */
export function useAccountNavigation() {
  const userStore = useUserStore()

  // Elementos de navegación principales
  const mainNavItems = computed(() => [
    {
      to: '/account/orders',
      icon: 'fas fa-shopping-bag',
      title: 'Mis Pedidos',
      subtitle: 'Ver historial de compras',
      badge: userStore.pendingOrdersCount || 0,
      priority: 1
    },
    {
      to: '/account/address',
      icon: 'fas fa-map-marker-alt',
      title: 'Direcciones',
      subtitle: 'Gestionar direcciones de envío',
      priority: 2
    },
    {
      to: '/account/details',
      icon: 'fas fa-user-edit',
      title: 'Datos Personales',
      subtitle: 'Actualizar información',
      priority: 3
    },
    {
      to: '/account/password',
      icon: 'fas fa-lock',
      title: 'Cambiar Contraseña',
      subtitle: 'Actualizar credenciales',
      priority: 4
    }
  ])

  // Elementos de navegación secundarios
  const secondaryNavItems = computed(() => [
    {
      to: '/account/preferences',
      icon: 'fas fa-cog',
      title: 'Preferencias',
      subtitle: 'Configurar cuenta',
      priority: 5
    },
    {
      to: '/account/notifications',
      icon: 'fas fa-bell',
      title: 'Notificaciones',
      subtitle: 'Gestionar avisos',
      badge: userStore.unreadNotifications || 0,
      priority: 6
    }
  ])

  // Todas las rutas de navegación ordenadas por prioridad
  const allNavItems = computed(() => {
    return [...mainNavItems.value, ...secondaryNavItems.value]
      .sort((a, b) => a.priority - b.priority)
  })

  // Elementos de navegación visibles (según permisos/rol)
  const visibleNavItems = computed(() => {
    return allNavItems.value.filter(item => {
      // Aquí se pueden agregar validaciones de permisos
      // Por ejemplo: return userStore.hasPermission(item.permission)
      return true
    })
  })

  // Obtener información de una ruta específica
  const getRouteInfo = (routeName) => {
    return allNavItems.value.find(item => 
      item.to.includes(routeName) || item.to.endsWith(routeName)
    )
  }

  // Validar si una ruta existe en la navegación
  const isValidRoute = (routePath) => {
    return allNavItems.value.some(item => item.to === routePath)
  }

  // Obtener la siguiente ruta en la navegación
  const getNextRoute = (currentRoute) => {
    const currentIndex = visibleNavItems.value.findIndex(item => item.to === currentRoute)
    if (currentIndex !== -1 && currentIndex < visibleNavItems.value.length - 1) {
      return visibleNavItems.value[currentIndex + 1].to
    }
    return null
  }

  // Obtener la ruta anterior en la navegación
  const getPreviousRoute = (currentRoute) => {
    const currentIndex = visibleNavItems.value.findIndex(item => item.to === currentRoute)
    if (currentIndex > 0) {
      return visibleNavItems.value[currentIndex - 1].to
    }
    return null
  }

  // Configuración de breadcrumbs
  const getBreadcrumbs = (currentRoute) => {
    const routeInfo = getRouteInfo(currentRoute)
    if (!routeInfo) return []

    return [
      { name: 'Inicio', to: '/' },
      { name: 'Mi Cuenta', to: '/account' },
      { name: routeInfo.title, to: routeInfo.to, active: true }
    ]
  }

  return {
    // Items de navegación
    mainNavItems,
    secondaryNavItems,
    allNavItems,
    visibleNavItems,
    
    // Métodos utilitarios
    getRouteInfo,
    isValidRoute,
    getNextRoute,
    getPreviousRoute,
    getBreadcrumbs
  }
}
