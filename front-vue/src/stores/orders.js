import { defineStore } from 'pinia'
import axios from 'axios'
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
        { method: 'post', url: '/api/pedidos/checkout' },
        { method: 'post', url: '/api/orders/checkout' },
        { method: 'post', url: '/api/pedidos' }
      ]
      try {
        let resp
        for (const t of tries) {
          try {
            resp = await axios({
              method: t.method,
              url: t.url,
              data: t.url === '/api/pedidos/checkout'
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
  const resp = await axios.get('/api/pedidos', { headers: this.authHeaders() })
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
        const resp = await axios.get(`/api/pedidos/${id}`, { headers: this.authHeaders() })
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
  // Backend usa valores como 'Imprimiendo', 'pendiente', 'pagado', 'enviado', 'cancelado'
  const e = (estado || '').toString().toLowerCase()
  if (!e) return 'processing'
  if (e.includes('imprim')) return 'processing'
  if (e === 'pendiente') return 'processing'
  if (e === 'procesando') return 'processing'
  if (e === 'pagado') return 'shipped'
  if (e === 'enviado') return 'shipped'
  if (e === 'cancelado') return 'cancelled'
  return 'processing'
}

function calcTotal(detalles = []) {
  return (detalles || []).reduce((acc, d) => acc + Number(d.precio_unitario || 0) * Number(d.cantidad || 1), 0).toFixed(2)
}