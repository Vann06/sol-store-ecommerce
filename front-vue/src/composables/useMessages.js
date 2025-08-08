import { ref, onMounted, readonly } from 'vue'
import { useRoute, useRouter } from 'vue-router'

export function useMessages() {
  const message = ref('')
  const messageType = ref('success')
  const isVisible = ref(false)
  const route = useRoute()
  const router = useRouter()

  let timeoutId = null

  onMounted(() => {
    // Verificar si hay un mensaje en la query
    if (route.query.message) {
      showMessage(route.query.message, route.query.messageType || 'success')
      
      // Limpiar la query después de mostrar el mensaje
      setTimeout(() => {
        const query = { ...route.query }
        delete query.message
        delete query.messageType
        router.replace({ query })
      }, 100)
    }
  })

  const showMessage = (text, type = 'success') => {
    // Limpiar timeout anterior si existe
    if (timeoutId) {
      clearTimeout(timeoutId)
    }
    
    message.value = text
    messageType.value = type
    isVisible.value = true
    
    // Auto-hide después de 5 segundos
    timeoutId = setTimeout(() => {
      hideMessage()
    }, 5000)
  }

  const hideMessage = () => {
    isVisible.value = false
    // Limpiar el mensaje después de la animación
    setTimeout(() => {
      message.value = ''
      messageType.value = 'success'
    }, 300)
  }

  // Cleanup cuando el componente se desmonte
  const cleanup = () => {
    if (timeoutId) {
      clearTimeout(timeoutId)
    }
  }

  return {
    // Estados readonly para evitar mutaciones externas
    message: readonly(message),
    messageType: readonly(messageType),
    isVisible: readonly(isVisible),
    
    // Métodos públicos
    showMessage,
    hideMessage,
    cleanup
  }
}
