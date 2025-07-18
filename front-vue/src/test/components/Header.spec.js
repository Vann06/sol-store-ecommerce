import { mount } from '@vue/test-utils'
import { describe, it, expect } from 'vitest'
import { createPinia } from 'pinia'
import { createRouter, createWebHistory } from 'vue-router'
import Header from '@/components/Header.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: []
})

describe('Header.vue', () => {
  it('renderiza correctamente el componente', () => {
    const wrapper = mount(Header, {
      global: {
        plugins: [createPinia(), router]
      }
    })
    expect(wrapper.exists()).toBe(true)
  })

  it('muestra el logo en el header', () => {
    const wrapper = mount(Header, {
      global: {
        plugins: [createPinia(), router]
      }
    })
    const logo = wrapper.find('img[alt="Logo"]')
    expect(logo.exists()).toBe(true)
  })

  it('incluye barra de búsqueda', () => {
    const wrapper = mount(Header, {
      global: {
        plugins: [createPinia(), router]
      }
    })
    const input = wrapper.find('input[type="text"]')
    expect(input.exists()).toBe(true)
  })
})
