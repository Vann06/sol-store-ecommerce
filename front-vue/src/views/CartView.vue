<template>
  <div class="cart-page">
    <div class="cart-container">
      <h1>Tu carrito</h1>
      <div class="cart-content">
        <!-- Productos en el carrito -->
        <div class="cart-items">
          <div v-if="loading">Cargando...</div>
          <div v-else-if="items.length === 0">Tu carrito está vacío.</div>
          <div v-else>
            <div class="cart-item" v-for="item in items" :key="item.id">
              <img :src="item.imagen" alt="Imagen producto" style="width:80px; margin-right:20px; border-radius:8px;">
              <div style="flex:1">
                <h3>{{ item.nombre }}</h3>
                <div class="item-details">
                  <span>Categoría: {{ item.categoria }} — Temática: {{ item.tematica }}</span>
                </div>
                <div class="item-controls">
                  <span class="price">${{ item.precio_unitario.toFixed(2) }}</span>
                  <div class="quantity-controls">
                    <button @click="updateQuantity(item, item.cantidad - 1)" :disabled="item.cantidad <= 1">—</button>
                    <span class="quantity">{{ item.cantidad }}</span>
                    <button @click="updateQuantity(item, item.cantidad + 1)">+</button>
                  </div>
                  <button class="remove-btn" @click="removeItem(item)">×</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Resumen del pedido -->
        <div class="order-summary">
          <h2>Resumen</h2>
          <div class="summary-row">
            <span>Subtotal</span>
            <span>$ {{ subtotal.toFixed(2) }}</span>
          </div>
          <div class="summary-row">
            <span>Envío:</span>
            <span class="free-shipping">Gratis</span>
          </div>
          <div class="summary-row">
            <span>Impuestos:</span>
            <span>$ {{ tax.toFixed(2) }}</span>
          </div>
          <div class="summary-row total">
            <span>Total</span>
            <span>$ {{ total.toFixed(2) }}</span>
          </div>
          <button class="checkout-btn">Finalizar compra</button>
          <button class="continue-btn" @click="$router.push('/')">Seguir comprando</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'ShoppingCart',
  data() {
    return {
      items: [],
      loading: true,
      taxRate: 0.0333 // 3.33%
    }
  },
  computed: {
    subtotal() {
      return this.items.reduce((total, item) => total + (item.precio_unitario * item.cantidad), 0)
    },
    tax() {
      return this.subtotal * this.taxRate
    },
    total() {
      return this.subtotal + this.tax
    }
  },
  methods: {
    async fetchCart() {
      this.loading = true
      try {
        const token = localStorage.getItem('auth_token')
        if (!token) {
          this.items = []
          this.loading = false
          return
        }
        const res = await fetch('http://localhost:8000/api/carrito', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        })
        const data = await res.json()
        this.items = data.items || []
      } catch (e) {
        this.items = []
      }
      this.loading = false
    },
    async updateQuantity(item, newQty) {
      if (newQty < 1) return
      try {
        const token = localStorage.getItem('auth_token')
        const res = await fetch(`http://localhost:8000/api/carrito/actualizar/${item.id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify({ cantidad: newQty })
        })
        if (res.ok) {
          this.fetchCart()
        } else {
          alert('No se pudo actualizar la cantidad')
        }
      } catch (e) {
        alert('No se pudo actualizar la cantidad')
      }
    },
    async removeItem(item) {
      try {
        const token = localStorage.getItem('auth_token')
        const res = await fetch(`http://localhost:8000/api/carrito/eliminar/${item.id}`, {
          method: 'DELETE',
          headers: {
            'Authorization': `Bearer ${token}`
          }
        })
        if (res.ok) {
          this.fetchCart()
        } else {
          alert('No se pudo eliminar el producto')
        }
      } catch (e) {
        alert('No se pudo eliminar el producto')
      }
    }
  },
  mounted() {
    this.fetchCart()
  },
  watch: {
    // Vuelve a cargar el carrito cada vez que la ruta cambia a este componente
    '$route'() {
      this.fetchCart()
    }
  }
}
</script>

<style scoped>
.cart-page {
  background-color: #f9f9f9;
  min-height: 100vh;
  padding: 20px 0;
}

.cart-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  font-family: 'Helvetica Neue', Arial, sans-serif;
}

h1 {
  font-size: 28px;
  margin-bottom: 30px;
  color: #333;
  font-weight: 500;
}

.cart-content {
  display: flex;
  gap: 30px;
}

.cart-items {
  flex: 2;
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.cart-item {
  border-bottom: 1px solid #eee;
  padding: 20px 0;
}

.cart-item h3 {
  margin: 0 0 10px 0;
  font-size: 18px;
  color: #333;
}

.item-details {
  color: #777;
  margin-bottom: 15px;
  font-size: 14px;
}

.item-controls {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.price {
  font-weight: bold;
  width: 80px;
  color: #7a0019; /* Color principal */
}

.quantity-controls {
  display: flex;
  align-items: center;
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 2px;
}

.quantity-controls button {
  background: #f5f5f5;
  border: none;
  padding: 5px 12px;
  cursor: pointer;
  font-size: 16px;
  color: #555;
}

.quantity-controls button:hover {
  background: #eee;
}

.quantity {
  margin: 0 15px;
  min-width: 20px;
  text-align: center;
}

.remove-btn {
  background: none;
  border: none;
  font-size: 22px;
  cursor: pointer;
  color: #999;
  padding: 0 10px;
}

.remove-btn:hover {
  color: #7a0019; /* Color principal */
}

/* Order Summary - Lado derecho */
.order-summary {
  flex: 1;
  background: white;
  padding: 25px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  height: fit-content;
  position: sticky;
  top: 20px;
}

.order-summary h2 {
  font-size: 20px;
  margin-bottom: 25px;
  color: #333;
  font-weight: 500;
  border-bottom: 1px solid #eee;
  padding-bottom: 15px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 15px;
  color: #555;
  font-size: 15px;
}

.summary-row.total {
  font-weight: bold;
  font-size: 18px;
  margin: 25px 0;
  color: #333;
}

.free-shipping {
  color: #f0c040; /* Color acento para envío gratis */
}

/* Botones */
.checkout-btn {
  background: #7a0019; /* Color principal */
  color: white;
  border: none;
  padding: 15px;
  width: 100%;
  font-size: 16px;
  cursor: pointer;
  margin-bottom: 15px;
  border-radius: 4px;
  font-weight: 500;
  transition: background 0.3s;
}

.checkout-btn:hover {
  background: #f0c040; /* Color acento */
  color: #7a0019;     /* Texto color principal */
}

.continue-btn {
  background: none;
  border: 1px solid #7a0019; /* Color principal */
  color: #7a0019;
  padding: 15px;
  width: 100%;
  font-size: 16px;
  cursor: pointer;
  border-radius: 4px;
  font-weight: 500;
  transition: all 0.3s;
}

.continue-btn:hover {
  background: #f0c040; /* Color acento */
  border-color: #7a0019; /* Color principal */
  color: #7a0019; /* Color principal */
}
</style>