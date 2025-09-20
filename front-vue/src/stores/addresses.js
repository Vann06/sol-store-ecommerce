import { defineStore } from 'pinia'
import axios from 'axios'
import { useUserStore } from './userStore'

export const useAddressesStore = defineStore('addresses', {
  state: () => ({
    items: [],
    loading: false,
    error: null,
  }),
  getters: {
    defaultId: (s) => s.items.find(a => a.is_default)?.id || null,
    defaultAddress: (s) => s.items.find(a => a.is_default) || null,
  },
  actions: {
    authHeaders() {
      const u = useUserStore()
      const token = u?.token || localStorage.getItem('auth_token')
      return token ? { Authorization: `Bearer ${token}` } : {}
    },
    async fetchAll() {
      this.loading = true
      this.error = null
      try {
        const r = await axios.get('/api/direcciones', { headers: this.authHeaders() })
        this.items = r.data || []
        return { success: true }
      } catch (e) {
        this.error = e.response?.data?.error || e.message
        return { success: false, error: this.error }
      } finally { this.loading = false }
    },
    async add({ direccion, id_municipio = null, is_default = false }) {
      try {
        const r = await axios.post('/api/direcciones', { direccion, id_municipio, is_default }, { headers: this.authHeaders() })
        if (is_default) this.items.forEach(a => a.is_default = false)
        this.items.push(r.data)
        return { success: true, data: r.data }
      } catch (e) { return { success: false, error: e.response?.data?.error || e.message } }
    },
    async update(id, payload) {
      try {
        const r = await axios.put(`/api/direcciones/${id}`, payload, { headers: this.authHeaders() })
        if (payload.is_default) this.items.forEach(a => a.is_default = false)
        const i = this.items.findIndex(a => a.id === id)
        if (i > -1) this.items[i] = r.data
        return { success: true, data: r.data }
      } catch (e) { return { success: false, error: e.response?.data?.error || e.message } }
    },
    async remove(id) {
      try {
        await axios.delete(`/api/direcciones/${id}`, { headers: this.authHeaders() })
        this.items = this.items.filter(a => a.id !== id)
        return { success: true }
      } catch (e) { return { success: false, error: e.response?.data?.error || e.message } }
      
    },
    async setDefault(id) {
      try {
        const r = await axios.post(`/api/direcciones/${id}/predeterminada`, {}, { headers: this.authHeaders() })
        this.items.forEach(a => a.is_default = a.id === id)
        return { success: true, data: r.data?.direccion }
      } catch (e) { return { success: false, error: e.response?.data?.error || e.message } }
    }
  }
})
