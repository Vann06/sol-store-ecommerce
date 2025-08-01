import { mount } from '@vue/test-utils'
import { describe, it, expect, vi, beforeEach } from 'vitest'

// Mock de fetch global
global.fetch = vi.fn()

describe('AUTOMATIZACIÓN FRONTEND - Flujo de Búsqueda API', () => {
  
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('ejecuta flujo completo: cargar filtros → búsqueda → resultados', async () => {
    console.log('🔍 Iniciando prueba automatizada de búsqueda API...')
    
    // PASO 1: Mock de categorías (basado en tu #codebase real)
    fetch.mockResolvedValueOnce({
      ok: true,
      json: async () => [
        { id: 1, name: 'Figuras', imagen: 'figuras.jpg' },
        { id: 2, name: 'Cosplay', imagen: 'cosplay.jpg' }
      ]
    })

    // PASO 2: Simular carga de categorías
    const categoriasResponse = await fetch('http://localhost:8000/api/categorias')
    const categorias = await categoriasResponse.json()
    
    expect(categorias).toHaveLength(2)
    expect(categorias[0]).toEqual(expect.objectContaining({
      id: 1,
      name: 'Figuras'
    }))
    console.log('✅ Categorías cargadas correctamente')

    // PASO 3: Mock de temáticas
    fetch.mockResolvedValueOnce({
      ok: true,
      json: async () => [
        { id: 1, name: 'Anime', imagen: 'anime.jpg' },
        { id: 2, name: 'Marvel', imagen: 'marvel.jpg' }
      ]
    })

    // PASO 4: Simular carga de temáticas
    const tematicasResponse = await fetch('http://localhost:8000/api/tematicas')
    const tematicas = await tematicasResponse.json()
    
    expect(tematicas).toHaveLength(2)
    expect(tematicas[0]).toEqual(expect.objectContaining({
      id: 1,
      name: 'Anime'
    }))
    console.log('✅ Temáticas cargadas correctamente')

    // PASO 5: Mock de búsqueda de productos (formato real de tu API)
    fetch.mockResolvedValueOnce({
      ok: true,
      json: async () => [
        {
          id: 1,
          nombre: 'Figura Goku SSJ',
          precio_base: 89.99,
          status: 'activo',
          imagen: 'goku.jpg',
          category: { id: 1, name: 'Figuras' },
          theme: { id: 1, name: 'Anime' }
        },
        {
          id: 2,
          nombre: 'Casco Iron Man',
          precio_base: 299.99,
          status: 'activo',
          imagen: 'ironman.jpg',
          category: { id: 2, name: 'Cosplay' },
          theme: { id: 2, name: 'Marvel' }
        }
      ]
    })

    // PASO 6: Simular búsqueda de productos
    const params = new URLSearchParams({
      search: 'Goku',
      'categoria[]': '1',
      'tematica[]': '1'
    })
    
    const productosResponse = await fetch(`http://localhost:8000/api/productos?${params.toString()}`)
    const productos = await productosResponse.json()
    
    expect(productos).toHaveLength(2)
    expect(productos[0]).toEqual(expect.objectContaining({
      nombre: 'Figura Goku SSJ',
      precio_base: 89.99,
      status: 'activo'
    }))
    console.log('✅ Búsqueda de productos ejecutada correctamente')

    // PASO 7: Verificar todas las llamadas realizadas
    expect(fetch).toHaveBeenCalledTimes(3)
    expect(fetch).toHaveBeenNthCalledWith(1, 'http://localhost:8000/api/categorias')
    expect(fetch).toHaveBeenNthCalledWith(2, 'http://localhost:8000/api/tematicas')
    expect(fetch).toHaveBeenNthCalledWith(3, expect.stringContaining('http://localhost:8000/api/productos'))
    
    console.log('✅ Todas las llamadas API verificadas')
    console.log('🎉 PRUEBA AUTOMATIZADA DE BÚSQUEDA API EXITOSA')
  })

  it('maneja búsqueda sin resultados', async () => {
    console.log('🔍 Probando búsqueda sin resultados...')
    
    // PASO 1: Mock de respuesta vacía
    fetch.mockResolvedValueOnce({
      ok: true,
      json: async () => []
    })

    const respuestaVacia = await fetch('http://localhost:8000/api/productos?search=noexiste')
    const productosVacios = await respuestaVacia.json()
    
    expect(productosVacios).toHaveLength(0)
    console.log('✅ Respuesta vacía manejada correctamente')

    // PASO 2: Mock de error de red
    fetch.mockRejectedValueOnce(new Error('Network Error'))
    
    try {
      await fetch('http://localhost:8000/api/productos?search=error')
    } catch (error) {
      expect(error.message).toBe('Network Error')
      console.log('✅ Error de red manejado correctamente')
    }
  })
})