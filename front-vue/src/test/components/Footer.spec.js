import { mount } from '@vue/test-utils'
import { describe, it, expect } from 'vitest'
import Footer from '@/components/Footer.vue'

describe('Footer.vue', () => {
  it('renderiza el footer', () => {
    const wrapper = mount(Footer)
    expect(wrapper.exists()).toBe(true)
  })

  it('contiene información de derechos de autor', () => {
    const wrapper = mount(Footer)
    expect(wrapper.text()).toMatch(/derechos reservados/i)
  })

  it('incluye enlaces a redes sociales', () => {
    const wrapper = mount(Footer)
    const links = wrapper.findAll('a')
    expect(links.length).toBeGreaterThanOrEqual(2)
  })
})
