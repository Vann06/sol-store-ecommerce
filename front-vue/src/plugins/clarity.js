/**
 * Plugin de Microsoft Clarity para Vue 3
 * Proporciona acceso global a las funciones de tracking
 */
import { useClarity } from '@/composables/useClarity'

export default {
  install(app) {
    // Crear instancia global de Clarity
    const clarityInstance = useClarity()
    
    // Hacer disponible globalmente como $clarity
    app.config.globalProperties.$clarity = clarityInstance
    
    // Proporcionar para uso con inject()
    app.provide('clarity', clarityInstance)
    
    // Métodos de conveniencia globales
    app.config.globalProperties.$trackEvent = clarityInstance.trackEvent
    app.config.globalProperties.$trackEcommerce = clarityInstance.trackEcommerce
    
    console.log('[Clarity Plugin] Plugin de Microsoft Clarity instalado correctamente')
  }
}
