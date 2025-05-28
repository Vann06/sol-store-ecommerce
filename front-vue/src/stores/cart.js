import { defineStore } from 'pinia'
import axios from 'axios'

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
    loading: false,
  }),
  actions: {
    async fetchCart() {
      this.loading = true
      const token = localStorage.getItem('auth_token')
      if (!token) {
        this.items = []
        this.loading = false
        return
      }
      const res = await axios.get('http://localhost:8000/api/carrito', {
        headers: { Authorization: `Bearer ${token}` }
      })
      this.items = res.data.items || []
      this.loading = false
    },
    async addToCart(producto_id, cantidad = 1) {
      const token = localStorage.getItem('auth_token')
      await axios.post('http://localhost:8000/api/carrito/agregar', {
        producto_id, cantidad
      }, {
        headers: { Authorization: `Bearer ${token}` }
      })
      await this.fetchCart()
    },
    async updateQuantity(detalle_id, cantidad) {
      const token = localStorage.getItem('auth_token')
      await axios.put(`http://localhost:8000/api/carrito/actualizar/${detalle_id}`, {
        cantidad
      }, {
        headers: { Authorization: `Bearer ${token}` }
      })
      await this.fetchCart()
    },
    async removeItem(detalle_id) {
      const token = localStorage.getItem('auth_token')
      await axios.delete(`http://localhost:8000/api/carrito/eliminar/${detalle_id}`, {
        headers: { Authorization: `Bearer ${token}` }
      })
      await this.fetchCart()
    }
  }
})
