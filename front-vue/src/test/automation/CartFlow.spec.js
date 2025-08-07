import { describe, it, expect, vi, beforeEach } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import { useCartStore } from '@/stores/cart'

// Mock correcto de axios (basado en tu #codebase)
vi.mock('axios', () => ({
  default: {
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    delete: vi.fn()
  }
}))

// Importar axios después del mock
import axios from 'axios'

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
    axios.get.mockResolvedValueOnce({
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
    expect(axios.get).toHaveBeenCalledWith(
      'http://localhost:8000/api/carrito',
      expect.objectContaining({
        headers: expect.objectContaining({
          Authorization: 'Bearer test-token-cart'
        })
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
    axios.post.mockResolvedValueOnce({
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
    axios.get.mockResolvedValueOnce({
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
    expect(axios.post).toHaveBeenCalledWith(
      'http://localhost:8000/api/carrito/agregar',
      {
        producto_id: 2,
        cantidad: 1
      },
      expect.objectContaining({
        headers: expect.objectContaining({
          Authorization: 'Bearer test-token-cart'
        })
      })
    )
    console.log('✅ Producto agregado al carrito')

    console.log('🎉 PRUEBA AUTOMATIZADA DE CARRITO EXITOSA')
  })

  it('maneja carrito sin autenticación', async () => {
    localStorage.removeItem('auth_token')
    
    await cartStore.fetchCart()
    
    expect(cartStore.items).toHaveLength(0)
    expect(axios.get).not.toHaveBeenCalled()
    console.log('✅ Carrito vacío sin autenticación')
  })
})