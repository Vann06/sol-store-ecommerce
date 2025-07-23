import { mount } from '@vue/test-utils'
import { describe, it, expect } from 'vitest'
import { createPinia } from 'pinia'
import { createRouter, createWebHistory } from 'vue-router'
import LoginForm from '@/components/LoginForm.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: []
})

describe('LoginForm.vue', () => {
  it('renderiza correctamente el formulario de login', () => {
    const wrapper = mount(LoginForm, {
      global: {
        plugins: [createPinia(), router]
      }
    })
    expect(wrapper.exists()).toBe(true)
  })

  it('incluye campo de email', () => {
    const wrapper = mount(LoginForm, {
      global: {
        plugins: [createPinia(), router]
      }
    })
    const emailInput = wrapper.find('input[type="email"]')
    expect(emailInput.exists()).toBe(true)
  })

  it('incluye campo de contraseña', () => {
    const wrapper = mount(LoginForm, {
      global: {
        plugins: [createPinia(), router]
      }
    })
    const passwordInput = wrapper.find('input[type="password"]')
    expect(passwordInput.exists()).toBe(true)
  })
})

