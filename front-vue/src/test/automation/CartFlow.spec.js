import { describe, it, expect, vi, beforeEach } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import { useCartStore } from '@/stores/cart'

// Mock de la instancia http para evitar llamadas reales
vi.mock('@/http.js', () => {
  const mockHttp = {
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    delete: vi.fn(),
  }
  return {
    default: mockHttp,
    BASE_URL: 'http://localhost:8000/api'
  }
})

// Importar http después del mock
import http from '@/http.js'

describe('AUTOMATIZACIÓN FRONTEND - Flujo de Carrito', () => {
  let cartStore
  let pinia

  beforeEach(() => {
    pinia = createPinia()
    setActivePinia(pinia)
    cartStore = useCartStore()
    vi.clearAllMocks()
    localStorage.setItem('auth_token', 'test-token-cart')
  })

  it('ejecuta flujo completo: mock de carrito → verificaciones', async () => {
    console.log('🛒 Iniciando prueba automatizada de carrito...')
    
    // PASO 1: Mock para fetchCart (basado en tu store real)
    http.get.mockResolvedValueOnce({
      data: {
        items: [
          {
            id: 1,
            producto_id: 1,
            nombre: 'Figura Goku Test',
            precio_unitario: 89.99,
            cantidad: 2,
            subtotal: 179.98
          }
        ],
        total: 179.98
      }
    })

    // PASO 2: Ejecutar fetchCart
    await cartStore.fetchCart()
    
    // PASO 3: Verificar que la llamada se hizo correctamente
    expect(http.get).toHaveBeenCalledWith(
      '/carrito',
      expect.objectContaining({
        headers: expect.any(Object)
      })
    )
    console.log('✅ Llamada a fetchCart ejecutada')

    // PASO 4: Verificar que el store se actualizó
    expect(cartStore.items).toHaveLength(1)
    expect(cartStore.items[0]).toEqual(expect.objectContaining({
      nombre: 'Figura Goku Test',
      cantidad: 2,
      precio_unitario: 89.99
    }))
    console.log('✅ Store de carrito actualizado correctamente')

    // PASO 5: Mock para addToCart
    http.post.mockResolvedValueOnce({
      data: {
        message: 'Producto agregado',
        item_agregado: {
          producto_id: 2,
          cantidad: 1,
          precio_unitario: 29.99
        }
      }
    })

    // Mock adicional para el fetchCart que se ejecuta después de addToCart
    http.get.mockResolvedValueOnce({
      data: {
        items: [
          {
            id: 1,
            producto_id: 1,
            nombre: 'Figura Goku Test',
            precio_unitario: 89.99,
            cantidad: 2,
            subtotal: 179.98
          },
          {
            id: 2,
            producto_id: 2,
            nombre: 'Nuevo Producto',
            precio_unitario: 29.99,
            cantidad: 1,
            subtotal: 29.99
          }
        ],
        total: 209.97
      }
    })

    // PASO 6: Ejecutar addToCart
    await cartStore.addToCart(2, 1)
    
    // PASO 7: Verificar llamada de addToCart
    expect(http.post).toHaveBeenCalledWith(
      '/carrito/agregar',
      {
        producto_id: 2,
        cantidad: 1
      },
      expect.objectContaining({
        headers: expect.any(Object)
      })
    )
    console.log('✅ Producto agregado al carrito')

    console.log('🎉 PRUEBA AUTOMATIZADA DE CARRITO EXITOSA')
  })

  it('maneja carrito sin autenticación', async () => {
    localStorage.removeItem('auth_token')
    
    // Mock respuesta vacía para usuario sin autenticación
    http.get.mockResolvedValueOnce({
      data: {
        items: [],
        total: 0
      }
    })
    
    await cartStore.fetchCart()
    
    // Verificar que la llamada se hizo (aunque sin token)
    expect(http.get).toHaveBeenCalledWith(
      '/carrito',
      expect.objectContaining({
        headers: expect.any(Object)
      })
    )
    
    // Verificar que el carrito está vacío
    expect(cartStore.items).toHaveLength(0)
    expect(cartStore.total).toBe(0)
    console.log('✅ Carrito vacío sin autenticación')
  })
})