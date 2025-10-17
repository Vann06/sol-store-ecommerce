// Global test setup for Vitest
// - Mocks axios calls to avoid real network requests
// - Silences expected warnings (router no-match, duplicate Pinia getter name)

import { beforeAll, afterEach, vi } from 'vitest'
import axios from 'axios'
import AxiosMockAdapter from 'axios-mock-adapter'
import http, { api as httpApi } from '@/http'
import api from '@/utils/http'

let mock
let mockHttp
let mockApi

beforeAll(() => {
  // Mock axios globally
  mock = new AxiosMockAdapter(axios, { delayResponse: 10 })

  // Default mocks for cart endpoints used in tests
  mock.onGet(/\/carrito$/).reply(200, { items: [], total: 0, cantidad_items: 0 })
  mock.onPost(/\/carrito\/agregar$/).reply(200, { success: true })
  mock.onDelete(/\/carrito\/vaciar$/).reply(200, { success: true })
  mock.onDelete(/\/carrito\/eliminar\/.+/).reply(200, { success: true })

  // Auth endpoints
  mock.onGet(/\/me$/).reply(200, { user: null })
  mock.onPost(/\/login$/).reply(401, { message: 'Unauthorized' })
  mock.onPost(/\/logout$/).reply(200, { success: true })

  // Fallback for any other API call to avoid jsdom network errors
  mock.onAny().reply((config) => {
    if (config.url?.startsWith('http')) {
      return [200, {}]
    }
    // For relative URLs, assume API; return 200 empty
    return [200, {}]
  })

  // Also mock axios instances created via axios.create used by the app
  mockHttp = new AxiosMockAdapter(http, { delayResponse: 10 })
  mockApi = new AxiosMockAdapter(api, { delayResponse: 10 })
  const mockHttpApi = new AxiosMockAdapter(httpApi, { delayResponse: 10 })

  const applyCommonRoutes = (m) => {
    m.onGet(/\/carrito$/).reply(200, { items: [], total: 0, cantidad_items: 0 })
    m.onPost(/\/carrito\/agregar$/).reply(200, { success: true })
    m.onDelete(/\/carrito\/vaciar$/).reply(200, { success: true })
    m.onDelete(/\/carrito\/eliminar\/.+/).reply(200, { success: true })
    m.onGet(/\/me$/).reply(200, { user: null })
    m.onPost(/\/login$/).reply(401, { message: 'Unauthorized' })
    m.onPost(/\/logout$/).reply(200, { success: true })
    m.onAny().reply(200, {})
  }
  applyCommonRoutes(mockHttp)
  applyCommonRoutes(mockApi)
  applyCommonRoutes(mockHttpApi)

  // Silence noisy console errors from router/pinia warnings in tests
  const originalError = console.error
  const originalWarn = console.warn
  console.error = (...args) => {
    const msg = args[0] || ''
    if (
      typeof msg === 'string' && (
        msg.includes('No match found for location with path') ||
        msg.includes('A getter cannot have the same name as another state property')
      )
    ) {
      return
    }
    originalError(...args)
  }

  console.warn = (...args) => {
    const msg = args[0] || ''
    if (typeof msg === 'string' && msg.includes('No match found for location with path')) {
      return
    }
    originalWarn(...args)
  }
})

afterEach(() => {
  // Reset handlers between tests while keeping defaults
  mock.resetHistory()
  mockHttp?.resetHistory()
  mockApi?.resetHistory()
})
