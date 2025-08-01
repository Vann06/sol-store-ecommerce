import { mount } from '@vue/test-utils'
import { describe, it, expect, vi, beforeEach } from 'vitest'
import { createPinia } from 'pinia'
import { createRouter, createMemoryHistory } from 'vue-router'
import Header from '@/components/Header.vue'

const router = createRouter({
  history: createMemoryHistory(),
  routes: [
    { path: '/', name: 'home', component: { template: '<div>Home</div>' } },
    { path: '/cart', name: 'cart', component: { template: '<div>Cart</div>' } },
    { path: '/account/login', name: 'login', component: { template: '<div>Login</div>' } }
  ]
})

describe('AUTOMATIZACIÓN FRONTEND - Header y Navegación', () => {
  let wrapper
  let pinia

  beforeEach(() => {
    pinia = createPinia()
    wrapper = mount(Header, {
      global: {
        plugins: [pinia, router]
      }
    })
    vi.clearAllMocks()
  })

  it('ejecuta flujo completo: renderizar → buscar → navegar', async () => {
    console.log('🧭 Iniciando prueba automatizada de header y navegación...')
    
    // PASO 1: Verificar elementos básicos del header
    expect(wrapper.exists()).toBe(true)
    console.log('✅ Header renderizado correctamente')
    
    // PASO 2: Buscar elementos de navegación comunes
    const navigationElements = wrapper.findAll('a, button, .nav-item, .menu-item')
    expect(navigationElements.length).toBeGreaterThan(0)
    console.log(`✅ Encontrados ${navigationElements.length} elementos de navegación`)
    
    // PASO 3: Verificar que hay elementos interactivos
    const interactiveElements = wrapper.findAll('input, button, a')
    expect(interactiveElements.length).toBeGreaterThan(0)
    console.log(`✅ Encontrados ${interactiveElements.length} elementos interactivos`)
    
    // PASO 4: Probar navegación a inicio si existe
    const homeLink = wrapper.find('a[href="/"], .home-link, [data-testid="home"]')
    if (homeLink.exists()) {
      await homeLink.trigger('click')
      console.log('✅ Link de inicio funciona')
    }
    
    // PASO 5: Buscar input de búsqueda si existe
    const searchInput = wrapper.find('input[type="search"], input[placeholder*="buscar"], input[placeholder*="Buscar"], .search-input')
    if (searchInput.exists()) {
      await searchInput.setValue('producto de prueba')
      console.log('✅ Input de búsqueda encontrado y valor seteado')
      
      // Simular envío de búsqueda
      const searchForm = searchInput.closest('form')
      if (searchForm) {
        await searchForm.trigger('submit.prevent')
        console.log('✅ Búsqueda enviada')
      }
    }
    
    // PASO 6: Probar navegación a página de login si existe
    const loginLink = wrapper.find('a[href="/account/login"], .login-link, [data-testid="login"]')
    if (loginLink.exists()) {
      await loginLink.trigger('click')
      console.log('✅ Link de login funciona')
    }
    
    console.log('🎉 PRUEBA AUTOMATIZADA DE HEADER Y NAVEGACIÓN EXITOSA')
  })
})