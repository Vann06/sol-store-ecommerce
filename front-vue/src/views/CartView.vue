<template>
  <!-- añade también el CSS var local para no afectar otras vistas -->
  <main class="cart-page under-fixed-header" style="--header-height: 96px;">
    <div class="cart-container">
      <!-- Header con el mismo estilo de gradientes que AccountDetailView -->
      <PageHeader
        title="Tu carrito"
        subtitle="Revisa tus productos y finaliza tu compra"
      />

      <div class="cart-content">
        <!-- Productos en el carrito -->
        <section class="cart-items" aria-live="polite">
          <!-- Loading / Skeleton -->
          <div v-if="loading" class="skeleton-list">
            <div class="skeleton-item" v-for="n in 3" :key="n">
              <div class="skeleton-img" />
              <div class="skeleton-text">
                <div class="bar w-60" />
                <div class="bar w-40" />
                <div class="bar w-80" />
              </div>
            </div>
          </div>

          <!-- Empty state -->
          <div v-else-if="items.length === 0" class="empty-state" data-testid="empty-cart">
            <div class="badge-gradient" aria-hidden="true">
              <svg
                class="icon-cart"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                aria-hidden="true"
              >
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.25 3h1.386a1.125 1.125 0 011.091.835l.383 1.438M6 7.5h13.09a1.125 1.125 0 011.103 1.36l-1.2 5.4a1.125 1.125 0 01-1.098.873H8.25m0 0a1.125 1.125 0 100 2.25h9.75a1.125 1.125 0 100-2.25M8.25 17.25a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zm10.5 0a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM5.11 5.273L6.75 12"
                />
              </svg>
              <span class="sr-only">Carrito</span>
            </div>
            <h3>Tu carrito está vacío</h3>
            <p>Explora nuestros productos y agrega tus favoritos.</p>
            <button class="continue-btn" @click="$router.push('/')">Ir a la tienda</button>
          </div>

          <!-- Lista de productos -->
          <div v-else class="items-stack">
            <article
              class="cart-item gradient-card"
              v-for="item in items"
              :key="item.id"
              data-testid="product-card"
            >
              <img
                :src="item.imagen"
                alt="Imagen del producto"
                class="item-img"
                @error="onImgError($event)"
              />

              <div class="item-body">
                <h3 class="item-title" data-testid="product-title">{{ item.nombre }}</h3>
                <div class="item-details">
                  <span>Categoría: {{ item.categoria }}</span>
                  <span class="dot">•</span>
                  <span>Temática: {{ item.tematica }}</span>
                </div>

                <div class="item-controls">
                  <span class="price">{{ formatCurrency(item.precio_unitario) }}</span>

                  <div class="quantity-controls" role="group" aria-label="Cantidad">
                    <button
                      class="qty-btn"
                      @click="updateQuantity(item, item.cantidad - 1)"
                      :disabled="item.cantidad <= 1"
                      aria-label="Disminuir cantidad"
                    >−</button>
                    <span class="quantity" aria-live="polite">{{ item.cantidad }}</span>
                    <button
                      class="qty-btn"
                      @click="updateQuantity(item, item.cantidad + 1)"
                      aria-label="Aumentar cantidad"
                    >+</button>
                  </div>

                  <button
                    class="remove-btn"
                    @click="removeItem(item)"
                    aria-label="Quitar producto"
                    data-testid="product-remove-btn"
                  >×</button>
                </div>
              </div>
            </article>
          </div>
        </section>

        <!-- Resumen del pedido -->
        <aside class="order-summary gradient-outline">
          <h2>Resumen</h2>
          <div class="summary-row">
            <span>Subtotal</span>
            <span>{{ formatCurrency(subtotal) }}</span>
          </div>
          <div class="summary-row">
            <span>Envío</span>
            <span class="free-shipping">Gratis</span>
          </div>
          <div class="summary-row">
            <span>Impuestos</span>
            <span>{{ formatCurrency(tax) }}</span>
          </div>
          <div class="summary-row total">
            <span>Total</span>
            <span>{{ formatCurrency(total) }}</span>
          </div>

          <button
            class="checkout-btn"
            :disabled="items.length === 0 || loading"
            data-testid="checkout-btn"
          >
            Finalizar compra
          </button>
          <button class="continue-btn" @click="$router.push('/')">Seguir comprando</button>
        </aside>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed } from 'vue'
import PageHeader from '@/components/PageHeader.vue'

// estado mínimo para que la vista no rompa
const loading = ref(false)
const items = ref([])

// helpers usados en el template
function formatCurrency(n) {
  const num = Number(n || 0)
  return num.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' })
}
function onImgError(e) {
  e.target.src = 'https://via.placeholder.com/96x96?text=IMG'
}
function updateQuantity(item, next) {
  const n = Math.max(1, Number(next) || 1)
  item.cantidad = n
}
function removeItem(item) {
  items.value = items.value.filter(i => i !== item)
}

// totales
const subtotal = computed(() =>
  items.value.reduce((acc, it) => acc + Number(it.precio_unitario || 0) * Number(it.cantidad || 1), 0)
)
const tax = computed(() => subtotal.value * 0.16)
const total = computed(() => subtotal.value + tax.value)
</script>

<style scoped>
/* Fondo de página con acentos de gradiente (consistente con health/account styles) */
.cart-page {
  --brand: #7a0019; /* vino */
  --brand-2: #a62535; /* vino claro */
  --accent: #f0c040; /* dorado */
  --accent-2: #e5bf60; /* dorado suave */
  --ink-1: #1f2937; /* gris oscuro */
  --ink-2: #6b7280; /* gris medio */
  --surface: #ffffff;

  background:
    radial-gradient(1200px 400px at 90% -10%, rgba(240,192,64,0.15), transparent 60%),
    radial-gradient(800px 300px at -10% 0%, rgba(122,0,25,0.12), transparent 55%),
    #f7f7f8;
  min-height: 100vh;
  padding: 24px 0 48px;
}

.cart-container {
  max-width: 1200px;
  margin: 0 auto;
  /* usa un único shorthand para no pisar padding-top */
  padding: calc(var(--header-height) + 24px) 20px 8px;
  font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji";
}

/* Layout principal */
.cart-content {
  display: grid;
  grid-template-columns: 1.5fr 0.9fr;
  gap: 28px;
}

@media (max-width: 1024px) {
  .cart-content { grid-template-columns: 1fr; }
}

/* Tarjetas con borde-gradiente (mismo lenguaje visual que account) */
.gradient-card {
  background: linear-gradient(#fff, #fff) padding-box,
              linear-gradient(135deg, rgba(240,192,64,0.5), rgba(122,0,25,0.4)) border-box;
  border: 1px solid transparent;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(31,41,55,0.06);
}

/* Lista de items */
.items-stack { display: grid; gap: 16px; }

.cart-items { padding: 20px; background: var(--surface); border-radius: 20px; box-shadow: 0 8px 24px rgba(31,41,55,0.06); border: 1px solid rgba(122,0,25,0.08); }

.cart-item { display: grid; grid-template-columns: 96px 1fr; gap: 16px; padding: 16px; }

.item-img { width: 96px; height: 96px; object-fit: cover; border-radius: 12px; }

.item-title { margin: 0 0 6px 0; font-size: 18px; color: var(--ink-1); font-weight: 600; }

.item-details { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; color: var(--ink-2); font-size: 14px; margin-bottom: 12px; }
.item-details .dot { opacity: 0.5; }

.item-controls { display: grid; grid-template-columns: 1fr auto auto; align-items: center; gap: 12px; }

.price { font-weight: 700; color: var(--brand); font-size: 16px; }

.quantity-controls { display: inline-flex; align-items: center; border: 1px solid #e5e7eb; border-radius: 999px; padding: 2px; background: #fafafa; }

.qty-btn { appearance: none; background: #fff; border: 0; padding: 6px 12px; border-radius: 999px; cursor: pointer; font-size: 18px; line-height: 1; box-shadow: 0 1px 0 rgba(17,24,39,0.04) inset; }
.qty-btn:hover { background: #f3f4f6; }
.qty-btn:disabled { opacity: .5; cursor: not-allowed; }

.quantity { display: inline-block; min-width: 24px; text-align: center; font-weight: 600; }

.remove-btn { background: transparent; border: 0; font-size: 24px; cursor: pointer; color: #a1a1aa; padding: 0 8px; border-radius: 8px; }
.remove-btn:hover { color: var(--brand); background: rgba(122,0,25,0.06); }

/* Resumen con borde gradiente y sticky */
.order-summary {
  position: sticky;
  top: calc(var(--header-height) + 20px);
  background: var(--surface);
  border-radius: 20px;
  padding: 24px;
  box-shadow: 0 12px 30px rgba(31,41,55,0.08);
}

.gradient-outline {
  background: linear-gradient(#fff, #fff) padding-box,
              linear-gradient(135deg, rgba(122,0,25,0.4), rgba(240,192,64,0.5)) border-box;
  border: 1px solid transparent;
}

.order-summary h2 { font-size: 20px; margin: 0 0 16px; color: var(--ink-1); font-weight: 700; }

.summary-row { display: flex; justify-content: space-between; margin: 12px 0; color: var(--ink-2); font-size: 15px; }
.summary-row.total { font-size: 18px; color: var(--ink-1); font-weight: 800; margin-top: 20px; padding-top: 12px; border-top: 1px dashed rgba(31,41,55,0.12); }

.free-shipping { color: var(--accent); font-weight: 700; }

/* Botones con gradiente consistente */
.checkout-btn {
  --bg: linear-gradient(135deg, var(--brand), var(--brand-2) 45%, var(--accent) 120%);
  width: 100%;
  border: none; color: #fff; font-weight: 700;
  padding: 14px 16px; border-radius: 12px; cursor: pointer; margin: 12px 0;
  background: var(--bg); background-size: 200% 100%; transition: background-position .3s ease, opacity .2s ease;
  box-shadow: 0 10px 20px rgba(122,0,25,0.18), 0 2px 4px rgba(0,0,0,0.04);
}
.checkout-btn:hover { background-position: 100% 0; }
.checkout-btn:disabled { opacity: .5; cursor: not-allowed; box-shadow: none; }

.continue-btn {
  width: 100%;
  background: linear-gradient(#fff, #fff) padding-box,
              linear-gradient(135deg, rgba(240,192,64,0.7), rgba(122,0,25,0.6)) border-box;
  border: 1px solid transparent; color: var(--brand);
  padding: 12px 16px; border-radius: 12px; cursor: pointer; font-weight: 700;
}
.continue-btn:hover { background: #fff7ea; }

/* Skeleton loader */
.skeleton-list { display: grid; gap: 16px; }
.skeleton-item { display: grid; grid-template-columns: 96px 1fr; gap: 16px; padding: 16px; border-radius: 16px; background: #fff; border: 1px solid #eee; }
.skeleton-img { width: 96px; height: 96px; border-radius: 12px; background: linear-gradient(90deg, #f3f4f6, #e5e7eb, #f3f4f6); background-size: 200% 100%; animation: shimmer 1.2s infinite; }
.skeleton-text { display: grid; gap: 8px; align-content: center; }
.bar { height: 12px; border-radius: 999px; background: linear-gradient(90deg, #f3f4f6, #e5e7eb, #f3f4f6); background-size: 200% 100%; animation: shimmer 1.2s infinite; }
.bar.w-60 { width: 60%; }
.bar.w-40 { width: 40%; }
.bar.w-80 { width: 80%; }
@keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

/* Empty state */
.empty-state { text-align: center; padding: 32px; background: #fff; border-radius: 20px; border: 1px dashed rgba(122,0,25,0.25); }
.badge-gradient {
  /* ya existe el bloque, sólo aseguramos tamaño del icono */
}
.badge-gradient .icon-cart {
  width: 36px;
  height: 36px;
  color: var(--brand);
}

.sr-only {
  position: absolute;
  width: 1px; height: 1px;
  padding: 0; margin: -1px;
  overflow: hidden; clip: rect(0,0,0,0);
  white-space: nowrap; border: 0;
}

/* Ajustes responsive */
@media (max-width: 640px) {
  .cart-items { padding: 14px; }
  .cart-item { grid-template-columns: 80px 1fr; padding: 12px; }
  .item-img { width: 80px; height: 80px; }
}
</style>
