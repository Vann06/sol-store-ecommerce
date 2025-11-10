<template>
  <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">📊 Reportes de Ventas</h1>
        <p class="mt-2 text-sm text-gray-600">
          Genera reportes detallados en PDF o Excel
        </p>
      </div>

      <!-- Filtros de Fecha -->
      <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">📅 Filtrar por Período</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Fecha Inicio
            </label>
            <input
              type="date"
              v-model="fechaInicio"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Fecha Fin
            </label>
            <input
              type="date"
              v-model="fechaFin"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          
          <div class="flex items-end">
            <button
              @click="cargarDatos"
              :disabled="cargando"
              class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
            >
              {{ cargando ? '⏳ Cargando...' : '🔍 Filtrar' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Resumen de Estadísticas -->
      <div v-if="datos" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Total Ventas</p>
              <p class="text-2xl font-bold text-green-600">
                Q{{ formatNumber(datos.resumen.total_ventas) }}
              </p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
              <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Total Pedidos</p>
              <p class="text-2xl font-bold text-blue-600">
                {{ datos.resumen.total_pedidos }}
              </p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
              <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Promedio Venta</p>
              <p class="text-2xl font-bold text-purple-600">
                Q{{ formatNumber(datos.resumen.promedio_venta) }}
              </p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
              <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Total Productos</p>
              <p class="text-2xl font-bold text-orange-600">
                {{ datos.resumen.total_productos }}
              </p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
              <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Botones de Exportación -->
      <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">📥 Exportar Reportes</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Ventas PDF -->
          <button
            @click="descargarPDF('ventas')"
            :disabled="descargando"
            class="flex items-center justify-center px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:bg-gray-400 transition-colors"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            Ventas PDF
          </button>

          <!-- Ventas Excel -->
          <button
            @click="descargarExcel('ventas')"
            :disabled="descargando"
            class="flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:bg-gray-400 transition-colors"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Ventas Excel
          </button>

          <!-- Productos PDF -->
          <button
            @click="descargarPDF('productos')"
            :disabled="descargando"
            class="flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-400 transition-colors"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            Productos PDF
          </button>

          <!-- Pedidos Excel -->
          <button
            @click="descargarExcel('pedidos')"
            :disabled="descargando"
            class="flex items-center justify-center px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:bg-gray-400 transition-colors"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Pedidos Excel
          </button>
        </div>
      </div>

      <!-- Gráficos -->
      <div v-if="datos" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Ventas por Mes -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Ventas por Mes</h3>
          <div class="h-64 flex items-end justify-around space-x-2">
            <div
              v-for="(venta, index) in datos.ventas_por_mes"
              :key="index"
              class="flex flex-col items-center flex-1"
            >
              <div class="text-xs font-semibold text-gray-700 mb-1">
                Q{{ formatNumber(venta.total) }}
              </div>
              <div
                class="w-full bg-blue-500 rounded-t-lg transition-all hover:bg-blue-600"
                :style="{ height: calcularAlturaBarra(venta.total, datos.ventas_por_mes) + 'px' }"
              ></div>
              <div class="text-xs text-gray-600 mt-2">{{ venta.mes }}</div>
            </div>
          </div>
        </div>

        <!-- Pedidos por Estado -->
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">📦 Pedidos por Estado</h3>
          <div class="space-y-3">
            <div
              v-for="(pedido, index) in datos.pedidos_por_estado"
              :key="index"
              class="flex items-center justify-between"
            >
              <div class="flex items-center space-x-3">
                <div
                  class="w-4 h-4 rounded-full"
                  :class="getColorEstado(pedido.estado)"
                ></div>
                <span class="text-sm font-medium text-gray-700">
                  {{ pedido.estado }}
                </span>
              </div>
              <div class="flex items-center space-x-4">
                <div class="w-48 bg-gray-200 rounded-full h-2.5">
                  <div
                    class="h-2.5 rounded-full transition-all"
                    :class="getColorBarraEstado(pedido.estado)"
                    :style="{ width: calcularPorcentaje(pedido.cantidad, datos.pedidos_por_estado) + '%' }"
                  ></div>
                </div>
                <span class="text-sm font-bold text-gray-900 w-8 text-right">
                  {{ pedido.cantidad }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Top Productos -->
        <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">🏆 Top 10 Productos Más Vendidos</h3>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    #
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Producto
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Ventas
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Stock
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(producto, index) in datos.top_productos" :key="index" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ index + 1 }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ producto.nombre }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                      {{ producto.ventas }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <span
                      class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                      :class="producto.stock > 10 ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800'"
                    >
                      {{ producto.stock }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Mensaje de carga inicial -->
      <div v-if="!datos && !error" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        <p class="mt-4 text-gray-600">Cargando datos...</p>
      </div>

      <!-- Mensaje de error -->
      <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700">
        <p class="font-semibold">❌ Error al cargar datos</p>
        <p class="text-sm mt-1">{{ error }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import http from '@/http'

// Estado
const datos = ref(null)
const cargando = ref(false)
const descargando = ref(false)
const error = ref(null)

// Fechas (último mes por defecto)
const fechaFin = ref(new Date().toISOString().split('T')[0])
const fechaInicio = ref(
  new Date(new Date().setMonth(new Date().getMonth() - 1))
    .toISOString()
    .split('T')[0]
)

// Funciones
const cargarDatos = async () => {
  cargando.value = true
  error.value = null
  
  try {
    const response = await http.get('/reportes/datos', {
      params: {
        fecha_inicio: fechaInicio.value,
        fecha_fin: fechaFin.value
      }
    })
    
    datos.value = response.data
  } catch (err) {
    console.error('Error al cargar datos:', err)
    error.value = err.response?.data?.message || 'Error al cargar los datos de reportes'
  } finally {
    cargando.value = false
  }
}

const descargarPDF = async (tipo) => {
  descargando.value = true
  
  try {
    // Construir query params para GET request
    const params = new URLSearchParams({
      tipo,
      fecha_inicio: fechaInicio.value,
      fecha_fin: fechaFin.value
    })
    
    // Obtener el token JWT
    const token = localStorage.getItem('access_token')
    
    // Hacer la petición con fetch para manejar mejor la descarga
    const response = await fetch(`${import.meta.env.VITE_API_BASE_URL || '/api'}/reportes/pdf?${params}`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/pdf'
      }
    })
    
    if (!response.ok) {
      throw new Error('Error al generar el PDF')
    }
    
    // Obtener el blob
    const blob = await response.blob()
    
    // Crear URL del blob y descargar
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `reporte_${tipo}_${new Date().toISOString().split('T')[0]}.pdf`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
  } catch (err) {
    console.error('Error al descargar PDF:', err)
    alert('Error al generar el PDF. Por favor, intenta de nuevo.')
  } finally {
    descargando.value = false
  }
}

const descargarExcel = async (tipo) => {
  descargando.value = true
  
  try {
    // Construir query params para GET request
    const params = new URLSearchParams({
      tipo,
      fecha_inicio: fechaInicio.value,
      fecha_fin: fechaFin.value
    })
    
    // Obtener el token JWT
    const token = localStorage.getItem('access_token')
    
    // Hacer la petición con fetch para manejar mejor la descarga
    const response = await fetch(`${import.meta.env.VITE_API_BASE_URL || '/api'}/reportes/excel?${params}`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
      }
    })
    
    if (!response.ok) {
      throw new Error('Error al generar el Excel')
    }
    
    // Obtener el blob
    const blob = await response.blob()
    
    // Crear URL del blob y descargar
    const url = window.URL.createObjectURL(blob)
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `reporte_${tipo}_${new Date().toISOString().split('T')[0]}.xlsx`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
  } catch (err) {
    console.error('Error al descargar Excel:', err)
    alert('Error al generar el archivo Excel. Por favor, intenta de nuevo.')
  } finally {
    descargando.value = false
  }
}

const formatNumber = (num) => {
  return new Intl.NumberFormat('es-GT', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(num || 0)
}

const calcularAlturaBarra = (valor, lista) => {
  const max = Math.max(...lista.map(v => v.total))
  return max > 0 ? (valor / max) * 200 : 0
}

const calcularPorcentaje = (valor, lista) => {
  const total = lista.reduce((sum, item) => sum + item.cantidad, 0)
  return total > 0 ? (valor / total) * 100 : 0
}

const getColorEstado = (estado) => {
  const colores = {
    'Procesando': 'bg-yellow-500',
    'Enviado': 'bg-blue-500',
    'Entregado': 'bg-green-500',
    'Cancelado': 'bg-red-500'
  }
  return colores[estado] || 'bg-gray-500'
}

const getColorBarraEstado = (estado) => {
  const colores = {
    'Procesando': 'bg-yellow-500',
    'Enviado': 'bg-blue-500',
    'Entregado': 'bg-green-500',
    'Cancelado': 'bg-red-500'
  }
  return colores[estado] || 'bg-gray-500'
}

// Cargar datos al montar el componente
onMounted(() => {
  cargarDatos()
})
</script>

<style scoped>
/* Animaciones */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.bg-white {
  animation: fadeIn 0.3s ease-in-out;
}
</style>
