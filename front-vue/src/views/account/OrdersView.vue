<template>
  <div class="orders-view">
    <div class="page-header">
      <h2><i class="fa fa-shopping-bag"></i> Mis Pedidos</h2>
      <p class="page-subtitle">Revisa el estado y detalles de tus compras</p>
    </div>

    <!-- Filtros -->
    <div class="filters-section">
      <div class="filter-tabs">
        <button 
          v-for="status in orderStatuses" 
          :key="status.key"
          @click="activeFilter = status.key"
          :class="['filter-tab', { active: activeFilter === status.key }]"
        >
          <i :class="status.icon"></i>
          {{ status.label }}
          <span class="badge" v-if="getOrderCount(status.key) > 0">
            {{ getOrderCount(status.key) }}
          </span>
        </button>
      </div>
    </div>

  <!-- Lista de pedidos -->
  <div class="orders-container" v-if="!isLoading && filteredOrders.length > 0">
      <div 
        v-for="order in filteredOrders" 
        :key="order.id" 
        class="order-card"
        @click="toggleOrderDetail(order.id)"
      >
        <div class="order-header">
          <div class="order-info">
            <h3>Pedido #{{ order.id }}</h3>
            <p class="order-date">{{ formatDate(order.date) }}</p>
          </div>
          <div class="order-status">
            <span :class="['status-badge', order.status]">
              <i :class="getStatusIcon(order.status)"></i>
              {{ getStatusLabel(order.status) }}
            </span>
          </div>
          <div class="order-total">
            <p class="total-label">Total</p>
            <p class="total-amount">${{ order.total }}</p>
          </div>
          <div class="expand-icon">
            <i :class="['fa', expandedOrders.includes(order.id) ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
          </div>
        </div>

        <!-- Detalles del pedido -->
        <div v-if="expandedOrders.includes(order.id)" class="order-details">
          <div class="order-items">
            <h4><i class="fa fa-box"></i> Productos</h4>
            <div class="items-list">
              <div
                v-for="d in (order.raw?.detalles || [])"
                :key="d.id"
                class="order-item"
              >
                <img
                  :src="d.detalle_producto?.producto?.imagen || '/img/no-results.svg'"
                  :alt="d.detalle_producto?.producto?.nombre || 'Producto'"
                  class="item-image"
                >
                <div class="item-info">
                  <h5>{{ d.detalle_producto?.producto?.nombre || 'Producto' }}</h5>
                  <p>Cantidad: {{ d.cantidad }}</p>
                  <p class="item-price">${{ Number(d.precio_unitario || 0).toFixed(2) }}</p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="order-actions">
            <button v-if="order.status === 'delivered'" class="btn btn-outline">
              <i class="fa fa-redo"></i>
              Reordenar
            </button>
            <button v-if="order.status === 'processing'" class="btn btn-danger">
              <i class="fa fa-times"></i>
              Cancelar
            </button>
            <button class="btn btn-primary">
              <i class="fa fa-truck"></i>
              Rastrear Pedido
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Estado vacío -->
  <div v-else class="empty-state" v-if="!isLoading">
      <div class="empty-icon">
        <i class="fa fa-shopping-bag"></i>
      </div>
      <h3>{{ getEmptyMessage() }}</h3>
      <p>{{ getEmptySubMessage() }}</p>
      <button @click="goToShop" class="btn btn-primary">
        <i class="fa fa-shopping-cart"></i>
        Comenzar a Comprar
      </button>
    </div>
  </div>
</template><script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useOrdersStore } from '@/stores/orders'

const router = useRouter()
const activeFilter = ref('all')
const expandedOrders = ref([])
const store = useOrdersStore()

onMounted(async () => {
  await store.fetchAll()
})

const isLoading = computed(() => store.loading)

const orderStatuses = [
  { key: 'all', label: 'Todos', icon: 'fa fa-list' },
  { key: 'processing', label: 'Procesando', icon: 'fa fa-clock' },
  { key: 'shipped', label: 'Enviado', icon: 'fa fa-shipping-fast' },
  { key: 'delivered', label: 'Entregado', icon: 'fa fa-check-circle' },
  { key: 'cancelled', label: 'Cancelado', icon: 'fa fa-times-circle' }
]

const filteredOrders = computed(() => {
  const list = store.items || []
  if (activeFilter.value === 'all') return list
  return list.filter(order => order.status === activeFilter.value)
})

const toggleOrderDetail = (orderId) => {
  const index = expandedOrders.value.indexOf(orderId)
  if (index > -1) {
    expandedOrders.value.splice(index, 1)
  } else {
    expandedOrders.value.push(orderId)
  }
}

const getOrderCount = (status) => {
  const list = store.items || []
  if (status === 'all') return list.length
  return list.filter(order => order.status === status).length
}

const getStatusIcon = (status) => {
  const icons = {
    processing: 'fa fa-clock',
    shipped: 'fa fa-shipping-fast',
    delivered: 'fa fa-check-circle',
    cancelled: 'fa fa-times-circle'
  }
  return icons[status] || 'fa fa-question-circle'
}

const getStatusLabel = (status) => {
  const labels = {
    processing: 'Procesando',
    shipped: 'Enviado',
    delivered: 'Entregado',
    cancelled: 'Cancelado'
  }
  return labels[status] || 'Desconocido'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getEmptyMessage = () => {
  if (activeFilter.value === 'all') {
    return 'No tienes pedidos aún'
  }
  const status = orderStatuses.find(s => s.key === activeFilter.value)
  return `No tienes pedidos ${status.label.toLowerCase()}`
}

const getEmptySubMessage = () => {
  if (activeFilter.value === 'all') {
    return 'Cuando realices tu primera compra, aparecerá aquí'
  }
  return 'Cambia el filtro para ver pedidos con otros estados'
}

const goToShop = () => {
  router.push('/')
}
</script>

<style scoped>
.orders-view {
  max-width: 1000px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 30px;
  text-align: center;
}

.page-header h2 {
  color: #333;
  font-size: 28px;
  font-weight: 600;
  margin-bottom: 8px;
}

.page-header h2 i {
  color: #7d1c2b;
  margin-right: 12px;
}

.page-subtitle {
  color: #666;
  font-size: 16px;
  margin: 0;
}

.filters-section {
  margin-bottom: 30px;
}

.filter-tabs {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  justify-content: center;
}

.filter-tab {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  border: 2px solid #E5E7EB;
  background: white;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
  position: relative;
}

.filter-tab:hover {
  border-color: #7d1c2b;
  background: #fdf8f0;
}

.filter-tab.active {
  border-color: #7d1c2b;
  background: #7d1c2b;
  color: white;
}

.badge {
  background: rgba(255, 255, 255, 0.3);
  color: inherit;
  padding: 2px 6px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
  min-width: 18px;
  text-align: center;
}

.filter-tab:not(.active) .badge {
  background: #7d1c2b;
  color: white;
}

.orders-container {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.order-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
}

.order-card:hover {
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
  transform: translateY(-2px);
}

.order-header {
  display: flex;
  align-items: center;
  padding: 20px;
  gap: 20px;
}

.order-info {
  flex: 1;
}

.order-info h3 {
  margin: 0 0 4px 0;
  font-size: 18px;
  font-weight: 600;
  color: #333;
}

.order-date {
  margin: 0;
  color: #666;
  font-size: 14px;
}

.order-status {
  flex-shrink: 0;
}

.status-badge {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
}

.status-badge.processing {
  background: #fef3c7;
  color: #a27345;
}

.status-badge.shipped {
  background: #dbeafe;
  color: #7d1c2b;
}

.status-badge.delivered {
  background: #e8f5e8;
  color: #22c55e;
}

.status-badge.cancelled {
  background: #FEE2E2;
  color: #991B1B;
}

.order-total {
  text-align: right;
  flex-shrink: 0;
}

.total-label {
  margin: 0 0 4px 0;
  color: #666;
  font-size: 12px;
  text-transform: uppercase;
  font-weight: 600;
}

.total-amount {
  margin: 0;
  font-size: 18px;
  font-weight: 700;
  color: #333;
}

.expand-icon {
  flex-shrink: 0;
  color: #9CA3AF;
}

.order-details {
  border-top: 1px solid #E5E7EB;
  padding: 20px;
  background: #FAFAFA;
}

.order-items h4 {
  margin: 0 0 15px 0;
  color: #333;
  display: flex;
  align-items: center;
  gap: 8px;
}

.items-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 20px;
}

.order-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: white;
  border-radius: 8px;
}

.item-image {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 6px;
}

.item-info {
  flex: 1;
}

.item-info h5 {
  margin: 0 0 4px 0;
  color: #333;
  font-size: 14px;
  font-weight: 600;
}

.item-info p {
  margin: 0 0 2px 0;
  color: #666;
  font-size: 12px;
}

.item-price {
  color: #333 !important;
  font-weight: 600 !important;
}

.order-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  flex-wrap: wrap;
}

.btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  border: none;
}

.btn-primary {
  background: linear-gradient(135deg, #7d1c2b 0%, #a27345 100%);
  color: white;
}

.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 15px rgba(125, 28, 43, 0.4);
}

.btn-outline {
  background: transparent;
  color: #7d1c2b;
  border: 1px solid #7d1c2b;
}

.btn-outline:hover {
  background: #7d1c2b;
  color: white;
}

.btn-danger {
  background: #EF4444;
  color: white;
}

.btn-danger:hover {
  background: #DC2626;
}

.empty-state {
  text-align: center;
  padding: 80px 20px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.empty-icon {
  font-size: 64px;
  color: #D1D5DB;
  margin-bottom: 20px;
}

.empty-state h3 {
  color: #333;
  margin-bottom: 8px;
  font-size: 24px;
}

.empty-state p {
  color: #666;
  margin-bottom: 30px;
  font-size: 16px;
}

/* Responsive */
@media (max-width: 768px) {
  .order-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
  
  .order-total {
    align-self: flex-end;
  }
  
  .filter-tabs {
    justify-content: flex-start;
    overflow-x: auto;
    padding-bottom: 8px;
  }
  
  .filter-tab {
    flex-shrink: 0;
  }
  
  .order-actions {
    justify-content: center;
  }
}
</style>
  