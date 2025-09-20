import { ref, onMounted } from 'vue'

/**
 * Composable para integrar Microsoft Clarity en la aplicación
 * Maneja la inicialización, tracking de eventos y configuración avanzada
 */
export function useClarity() {
  const isInitialized = ref(false)
  const isEnabled = ref(import.meta.env.VITE_CLARITY_ENABLED === 'true')
  const projectId = import.meta.env.VITE_CLARITY_PROJECT_ID

  /**
   * Inicializa Microsoft Clarity
   */
  const initClarity = () => {
    // Verificar si está habilitado y configurado
    if (!isEnabled.value || !projectId) {
      console.warn('[Clarity] Microsoft Clarity no está habilitado o configurado')
      return
    }

    // Verificar si ya está inicializado
    if (window.clarity || isInitialized.value) {
      console.log('[Clarity] Ya está inicializado')
      return
    }

    try {
      // Script de inicialización de Microsoft Clarity
      ;(function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
      })(window, document, "clarity", "script", projectId);

      isInitialized.value = true
      console.log('[Clarity] Microsoft Clarity inicializado correctamente')
      
      // Configuraciones adicionales después de inicializar
      setTimeout(() => {
        configureClarity()
      }, 1000)

    } catch (error) {
      console.error('[Clarity] Error al inicializar Microsoft Clarity:', error)
    }
  }

  /**
   * Configuraciones adicionales de Clarity
   */
  const configureClarity = () => {
    if (!window.clarity) return

    // Configurar cookies y privacidad si es necesario
    window.clarity('consent')
    
    // Configurar identificación de usuario si está logueado
    const userStore = JSON.parse(localStorage.getItem('user') || '{}')
    if (userStore.id) {
      identifyUser(userStore.id, {
        email: userStore.email,
        role: userStore.role
      })
    }
  }

  /**
   * Identifica al usuario en Clarity
   * @param {string|number} userId - ID único del usuario
   * @param {Object} customData - Datos adicionales del usuario
   */
  const identifyUser = (userId, customData = {}) => {
    if (!window.clarity || !userId) return

    try {
      window.clarity('identify', userId.toString(), customData)
      console.log('[Clarity] Usuario identificado:', userId)
    } catch (error) {
      console.error('[Clarity] Error al identificar usuario:', error)
    }
  }

  /**
   * Envía un evento personalizado a Clarity
   * @param {string} eventName - Nombre del evento
   * @param {Object} eventData - Datos del evento
   */
  const trackEvent = (eventName, eventData = {}) => {
    if (!window.clarity || !isEnabled.value) return

    try {
      // Agregar contexto adicional al evento
      const enrichedData = {
        ...eventData,
        timestamp: new Date().toISOString(),
        url: window.location.href,
        userAgent: navigator.userAgent.substring(0, 100) // Limitar longitud
      }

      window.clarity('event', eventName, enrichedData)
      console.log(`[Clarity] Evento tracked: ${eventName}`, enrichedData)
    } catch (error) {
      console.error('[Clarity] Error al enviar evento:', error)
    }
  }

  /**
   * Eventos específicos del e-commerce
   */
  const trackEcommerce = {
    // Eventos de autenticación
    login: (method = 'email') => {
      trackEvent('user_login', { method })
    },

    logout: () => {
      trackEvent('user_logout')
    },

    register: (method = 'email') => {
      trackEvent('user_register', { method })
    },

    // Eventos de producto
    viewProduct: (productId, productName, category = '') => {
      trackEvent('product_view', {
        product_id: productId,
        product_name: productName,
        category
      })
    },

    addToCart: (productId, productName, quantity = 1, price = 0) => {
      trackEvent('add_to_cart', {
        product_id: productId,
        product_name: productName,
        quantity,
        price,
        value: price * quantity
      })
    },

    removeFromCart: (productId, productName) => {
      trackEvent('remove_from_cart', {
        product_id: productId,
        product_name: productName
      })
    },

    // Eventos de checkout
    beginCheckout: (cartValue, itemCount) => {
      trackEvent('begin_checkout', {
        value: cartValue,
        items: itemCount
      })
    },

    purchase: (orderId, value, items) => {
      trackEvent('purchase', {
        transaction_id: orderId,
        value,
        items
      })
    },

    // Eventos de búsqueda
    search: (query, resultsCount = 0) => {
      trackEvent('search', {
        search_term: query,
        results_count: resultsCount
      })
    },

    // Eventos de navegación
    pageView: (pageName, category = '') => {
      trackEvent('page_view', {
        page_name: pageName,
        page_category: category
      })
    },

    // Eventos de formulario
    formStart: (formName) => {
      trackEvent('form_start', { form_name: formName })
    },

    formSubmit: (formName, success = true) => {
      trackEvent('form_submit', {
        form_name: formName,
        success
      })
    }
  }

  /**
   * Configurar seguimiento automático de clics en elementos importantes
   */
  const setupAutoTracking = () => {
    if (!window.clarity) return

    // Auto-track botones importantes
    const trackButtons = () => {
      document.addEventListener('click', (event) => {
        const element = event.target.closest('button, .btn, [role="button"]')
        if (element) {
          const buttonText = element.textContent?.trim() || 'Unknown Button'
          const buttonClass = element.className || 'no-class'
          
          trackEvent('button_click', {
            button_text: buttonText.substring(0, 50),
            button_class: buttonClass.substring(0, 100),
            element_id: element.id || 'no-id'
          })
        }
      })
    }

    // Auto-track enlaces externos
    const trackExternalLinks = () => {
      document.addEventListener('click', (event) => {
        const link = event.target.closest('a')
        if (link && link.href) {
          const isExternal = link.hostname !== window.location.hostname
          if (isExternal) {
            trackEvent('external_link_click', {
              url: link.href,
              text: link.textContent?.trim().substring(0, 50) || 'No text'
            })
          }
        }
      })
    }

    setTimeout(() => {
      trackButtons()
      trackExternalLinks()
    }, 2000)
  }

  /**
   * Configuración de error tracking
   */
  const setupErrorTracking = () => {
    // Capturar errores JavaScript
    window.addEventListener('error', (event) => {
      trackEvent('javascript_error', {
        message: event.message,
        filename: event.filename,
        lineno: event.lineno,
        colno: event.colno
      })
    })

    // Capturar promesas rechazadas
    window.addEventListener('unhandledrejection', (event) => {
      trackEvent('unhandled_promise_rejection', {
        reason: event.reason?.toString() || 'Unknown error'
      })
    })
  }

  // Inicializar automáticamente cuando el componente se monta
  onMounted(() => {
    initClarity()
    
    // Configurar tracking adicional después de un delay
    setTimeout(() => {
      setupAutoTracking()
      setupErrorTracking()
    }, 3000)
  })

  return {
    // Estado
    isInitialized,
    isEnabled,
    
    // Métodos principales
    initClarity,
    trackEvent,
    identifyUser,
    
    // Métodos específicos del e-commerce
    trackEcommerce,
    
    // Utilidades
    configureClarity,
    setupAutoTracking,
    setupErrorTracking
  }
}
