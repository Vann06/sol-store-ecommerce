import { defineStore } from 'pinia'
import http from '@/http'
import { useUserStore } from './userStore'

export const useOrdersStore = defineStore('orders', {
  state: () => ({
    loading: false,
    error: null,
  lastOrder: null,
  items: [],
  current: null
  }),
  actions: {
    authHeaders() {
      const user = useUserStore()
      const token =
        user?.token ||
        localStorage.getItem('auth_token') ||
        localStorage.getItem('access_token')
      return token ? { Authorization: `Bearer ${token}` } : {}
    },
  async checkout({ direccion_id = null, direccion_envio = null }) {
      this.loading = true
      this.error = null
      this.lastOrder = null
      // Soporta varios endpoints posibles del backend
      const tries = [
        { method: 'post', url: '/pedidos/checkout' },
        { method: 'post', url: '/orders/checkout' },
        { method: 'post', url: '/pedidos' }
      ]
      try {
        let resp
        for (const t of tries) {
          try {
            resp = await http({
              method: t.method,
              url: t.url,
              data: t.url === '/pedidos/checkout'
                ? { direccion_id }
                : { direccion_envio },
              headers: this.authHeaders()
            })
            if (resp?.status >= 200 && resp?.status < 300) break
          } catch (e) {
            resp = null
            // intenta siguiente
          }
        }
        if (!resp) throw new Error('No se pudo contactar el endpoint de pedidos')
        this.lastOrder = resp.data?.pedido || resp.data
        return { success: true, data: resp.data }
      } catch (e) {
  this.error = e.response?.data?.error || e.message
  const status = e.response?.status
  return { success: false, error: this.error, status }
      } finally {
        this.loading = false
      }
    },
    async fetchAll() {
      this.loading = true
      this.error = null
      try {
  const resp = await http.get('/pedidos', { headers: this.authHeaders() })
  // Quedarnos con pedidos que provienen del checkout (tienen detalles)
  const pedidos = (resp.data || []).filter(p => Array.isArray(p.detalles) && p.detalles.length > 0)
  // Normalizar estructura hacia el frontend
  this.items = pedidos.map(p => ({
          id: p.id,
          date: p.fecha_pedido || p.created_at,
          status: mapEstado(p.estado),
          total: calcTotal(p.detalles),
          // Mantén los detalles crudos para expandir
          raw: p
        }))
        return { success: true }
      } catch (e) {
        this.error = e.response?.data?.error || e.message
        const status = e.response?.status
        return { success: false, error: this.error, status }
      } finally {
        this.loading = false
      }
    },
    async fetchOne(id) {
      this.loading = true
      this.error = null
      try {
        const resp = await http.get(`/pedidos/${id}`, { headers: this.authHeaders() })
        this.current = resp.data
        return { success: true, data: resp.data }
      } catch (e) {
        this.error = e.response?.data?.error || e.message
        const status = e.response?.status
        return { success: false, error: this.error, status }
      } finally {
        this.loading = false
      }
    }
  }
})

// Helpers locales
function mapEstado(estado) {
  const e = (estado || '').toString().toLowerCase()
  if (e === 'procesando') return 'processing'
  if (e === 'enviado') return 'shipped'
  if (e === 'entregado') return 'delivered'
  if (e === 'cancelado') return 'cancelled'
  // Legacy mappings
  if (e.includes('imprim') || e === 'pendiente') return 'processing'
  if (e === 'pagado') return 'shipped'
  return 'processing'
}

function calcTotal(detalles = []) {
  return (detalles || []).reduce((acc, d) => acc + Number(d.precio_unitario || 0) * Number(d.cantidad || 1), 0).toFixed(2)
}