<template>
  <header class="header" ref="headerRef">
    <div class="header__container">
      <!-- Botón hamburguesa -->
      <button class="hamburger" @click="toggleMenu" aria-label="Abrir menú">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </button>

      <!-- LOGO -->
      <div class="header__logo">
        <RouterLink to="/">
          <img src="/img/logo.png" alt="Logo" />
        </RouterLink>
      </div>

      <!-- Lado derecho (móvil): icono de búsqueda + carrito -->
      <div class="header__top-right">
        <button class="search-icon-btn" @click="openMobileSearch" aria-label="Buscar">
          <img src="/img/search-icon.svg" alt="Buscar" class="icon-img" />
        </button>
        <CartWidget />
      </div>

      <!-- Contenedor combinado -->
      <div class="header__content" :class="{ open: isMenuOpen }">
        <nav class="header__nav">
          <ul>
            <li><RouterLink to="/" @click.native="closeMenuOnNavigate">Inicio</RouterLink></li>
            <li><RouterLink to="/catalogo" @click.native="closeMenuOnNavigate">Catálogo</RouterLink></li>
            <li><RouterLink to="/about" @click.native="closeMenuOnNavigate">Quiénes Somos</RouterLink></li>
            <li><RouterLink to="/contact" @click.native="closeMenuOnNavigate">Contacto</RouterLink></li>
            <li><RouterLink to="/faq" @click.native="closeMenuOnNavigate">Preguntas</RouterLink></li>
          </ul>
        </nav>

  <div class="header__tools">
          <div class="search-box">
            <input
              ref="desktopSearchInput"
              type="text"
              v-model="searchText"
              @keyup.enter="handleSearch"
              placeholder="Buscar productos"
            />
            <button
              v-if="isSearchEmpty"
              class="icon-btn"
              @click="handleSearch"
              aria-label="Buscar"
              title="Buscar"
            >
              <img src="/img/search-icon.svg" alt="" class="icon-img" />
            </button>
            <button
              v-else
              class="icon-btn clear-btn"
              @click="clearSearch('desktop')"
              aria-label="Limpiar búsqueda"
              title="Limpiar búsqueda"
            >
              ×
            </button>
          </div>

          <CartWidget />

          <!-- SI ESTÁ LOGUEADO -->
          <div v-if="isLoggedIn" class="user-logged">
            <RouterLink to="/account/orders" class="icon-button">
              <img src="/img/user-icon.svg" alt="Usuario" class="icon-img" />
              <span class="username">{{ user.first_name }}</span>
            </RouterLink>
            <button @click="logout" class="icon-button logout-btn" title="Cerrar sesión">
              <i class="fa fa-sign-out-alt"></i>
            </button>
          </div>

          <!-- SI NO ESTÁ LOGUEADO -->
          <RouterLink v-else to="/account/login" class="icon-button" title="Iniciar sesión">
            <img src="/img/user-icon.svg" alt="Usuario" class="icon-img" />
          </RouterLink>

        </div>
      </div>
    </div>
  </header>

  <!-- Overlay de búsqueda en móvil -->
  <Transition name="fade">
    <div v-if="isSearchOpen" class="mobile-search-overlay" @click.self="closeMobileSearch">
      <div class="mobile-search-panel">
        <div class="mobile-search-header">
          <button class="close-overlay" @click="closeMobileSearch" aria-label="Cerrar búsqueda">×</button>
          <h3>Buscar</h3>
        </div>
        <div class="mobile-search-body">
          <div class="search-input-wrap">
            <input
              ref="mobileSearchInput"
              type="search"
              v-model="searchText"
              @keyup.enter="handleSearchAndClose"
              placeholder="Buscar productos..."
              aria-label="Buscar productos"
            />
            <button
              v-if="isSearchEmpty"
              class="search-submit"
              @click="handleSearchAndClose"
              aria-label="Buscar"
              title="Buscar"
            >
              <img src="/img/search-icon.svg" alt="" class="icon-img" />
            </button>
            <button
              v-else
              class="search-submit clear-btn"
              @click="clearSearch('mobile')"
              aria-label="Limpiar búsqueda"
              title="Limpiar búsqueda"
            >
              ×
            </button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useUserStore } from '@/stores/userStore'
import {computed} from 'vue'
import { useLogout } from '@/composables/useLogout'
import CartWidget from '@/components/CartWidget.vue'

const userStore = useUserStore()
const isLoggedIn = computed(() => userStore.isAuthenticated)
const user = computed(() => userStore.user)
const userName = computed(() => userStore.userName)
const { logout } = useLogout()

const isMenuOpen = ref(false)
const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
}
const closeMenuOnNavigate = () => {
  // Only close in mobile where the menu can be toggled open
  if (isMenuOpen.value) isMenuOpen.value = false
}

const searchText = ref('')
const router = useRouter()
const route = useRoute()
const isSearchEmpty = computed(() => !searchText.value || searchText.value.trim() === '')

// Nota: En el menú no mostramos "Perfil"; se deja el icono existente en la barra.

function handleSearch() {
  const q = searchText.value.trim()
  if (!q) return
  router.push({ name: 'catalogo', query: { q } })
}

function clearSearch(origin = 'desktop') {
  searchText.value = ''
  if (origin === 'desktop') {
    desktopSearchInput.value?.focus()
  }
}

// Búsqueda móvil
const isSearchOpen = ref(false)
const mobileSearchInput = ref(null)
const openMobileSearch = async () => {
  isSearchOpen.value = true
  await nextTick()
  mobileSearchInput.value?.focus()
}
const closeMobileSearch = () => {
  isSearchOpen.value = false
}
const handleSearchAndClose = () => {
  handleSearch()
  closeMobileSearch()
}

// Dinámicamente calcular la altura del header fijo y exponerla como CSS var
const headerRef = ref(null)
const setHeaderHeight = () => {
  const h = headerRef.value?.offsetHeight || 96
  document.documentElement.style.setProperty('--header-height', `${h}px`)
}

onMounted(() => {
  setHeaderHeight()
  // Recalcular en resize y al terminar de cargar fuentes/recursos
  window.addEventListener('resize', setHeaderHeight)
  window.addEventListener('load', setHeaderHeight)
  // Recalcular tras abrir/cerrar menú (puede cambiar la altura en mobile)
  watch(isMenuOpen, async () => {
    await nextTick()
    setHeaderHeight()
  })
  // Cerrar menú después de cada navegación de ruta (mobile UX)
  router.afterEach(() => {
    if (isMenuOpen.value) isMenuOpen.value = false
  })
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', setHeaderHeight)
  window.removeEventListener('load', setHeaderHeight)
})
</script>

<style scoped>
.header {
  background-color: #fff;
  border-top: 5px solid var(--brand-strong);
  box-shadow: 0 1px 10px rgba(0, 0, 0, 0.1);
  width: 100%;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 100;
}

.header__container {
  max-width: 1300px;
  margin: 0 auto;
  padding: 10px 30px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
}

.header__logo img {
  height: 60px;
}

.header__content {
  display: flex;
  align-items: center;
  gap: 30px;
}

.header__nav ul {
  list-style: none;
  display: flex;
  align-items: center;
  justify-content: center; /* centrar items en el contenedor */
  margin: 0;
  padding: 0;
  gap: 20px;
}

.header__nav ul li {
  padding: 0; /* el hover solo sobre el enlace */
  border-radius: 0;
  transition: none;
  cursor: pointer;
}

.header__nav ul li:hover { background: transparent; }

.header__nav a {
  text-decoration: none;
  color: #333;
  font-weight: 500;
  font-size: 15px;
  padding: 8px 12px; /* tamaño justo del hover */
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

/* Mantener opción activa en rojo */
.header__nav a.router-link-active,
.header__nav a.router-link-exact-active {
  background-color: var(--brand-strong);
  color: #fff;
}

/* Hover aplicado al enlace (no al li) */
.header__nav a:hover {
  background-color: var(--brand-strong);
  color: #fff;
}

.dropdown-icon {
  font-size: 10px;
  margin-left: 4px;
}

.header__tools {
  display: flex;
  align-items: center;
  gap: 15px;
}

.search-box {
  display: flex;
  align-items: center;
  gap: 8px;
  border: 1px solid #ddd;
  padding: 6px 12px;
  border-radius: 6px;
  background-color: #fff;
}

.search-box input {
  border: none;
  outline: none;
  font-size: 14px;
  width: 160px;
}

.icon-img {
  width: 20px;
  height: 20px;
}

.icon-btn {
  background: none;
  border: none;
  padding: 6px;
  cursor: pointer;
  line-height: 1;
  border-radius: 6px;
}
.icon-btn.clear-btn {
  font-size: 18px;
  font-weight: 700;
  color: #555;
}

/* Estilos para usuario logueado */
.user-logged {
  display: flex;
  align-items: center;
  gap: 10px;
}

.username {
  font-size: 14px;
  font-weight: 500;
  color: #333;
}

.logout-btn {
  background: none;
  border: none;
  color: var(--brand-strong);
  font-size: 16px;
  cursor: pointer;
  padding: 5px;
  border-radius: 4px;
  transition: all 0.3s ease;
}

.logout-btn:hover {
  background-color: var(--brand-strong);
  color: white;
}

.icon-button {
  display: flex;
  align-items: center;
  gap: 5px;
  text-decoration: none;
  color: #333;
  transition: opacity 0.3s ease;
}

.icon-button:hover {
  opacity: 0.7;
}

/* Botón hamburguesa oculto por defecto */
.hamburger {
  display: none;
  flex-direction: column;
  gap: 4px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 10px;
}

.hamburger .bar {
  width: 25px;
  height: 3px;
  background-color: #333;
  border-radius: 2px;
}

/* Responsive */
@media (max-width: 768px) {
  .hamburger {
    display: flex;
  }

  /* Reordenar layout: hamburguesa izquierda, logo centro, búsqueda+carrito derecha */
  .header__container {
    display: grid;
    grid-template-columns: auto 1fr auto;
    align-items: center;
  }
  .hamburger { order: 1; justify-self: start; }
  .header__logo { order: 2; justify-self: center; }
  .header__top-right { order: 3; justify-self: end; display: flex; align-items: center; gap: 10px; }
  .header__content { order: 4; grid-column: 1 / -1; }

  /* Panel móvil deslizante desde la izquierda */
  .header__content {
    position: fixed;
    top: var(--header-height, 96px);
    left: 0;
    width: 85vw;
    height: calc(100vh - var(--header-height, 96px));
    background: #fff;
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding: 16px;
    box-shadow: 6px 0 20px rgba(0,0,0,0.15);
    transform: translateX(-100%);
    transition: transform 0.3s ease;
    z-index: 110;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
  }

  .header__content.open {
    transform: translateX(0);
  }

  .header__nav ul {
    flex-direction: column;
    gap: 10px;
    align-items: center; /* centrar vertical stack */
  }

  .header__tools {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }

  /* En móvil: ocultar los iconos dentro del panel, mostrar búsqueda+carrito arriba a la derecha */
  .header__tools { display: none; }
  .user-logged, .icon-button { display: none; }
  /* No mostrar cart dentro del panel para evitar duplicado */
  .header__content :deep(.cart-widget) { display: none; }
}

/* Desktop: ocultar contenedor top-right móvil */
@media (min-width: 769px) {
  .header__top-right { display: none; }


  .search-box input {
    width: 100%;
  }
  .header__nav ul li, .icon-button, .search-box input, .logout-btn {
    min-height: 44px; /* Better tap targets */
  }

  .search-box { width: 100%; }
  .search-box input { width: 100%; font-size: 16px; } /* avoid zoom on iOS */
}

/* Botón de icono de búsqueda (móvil) */
.search-icon-btn {
  background: none;
  border: none;
  padding: 8px;
  cursor: pointer;
}

/* Overlay de búsqueda móvil */
.mobile-search-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  z-index: 120;
}
.mobile-search-panel {
  position: absolute;
  left: 0;
  right: 0;
  top: var(--header-height, 96px);
  background: #fff;
  border-radius: 12px 12px 0 0;
  padding: 16px;
  box-shadow: 0 -6px 24px rgba(0,0,0,0.2);
}
.mobile-search-header {
  display: flex;
  align-items: center;
  gap: 12px;
}
.mobile-search-header h3 {
  margin: 0;
  color: #333;
  font-weight: 700;
}
.close-overlay {
  background: none;
  border: none;
  font-size: 24px;
  line-height: 1;
  cursor: pointer;
  margin-right: 8px;
}
.mobile-search-body {
  margin-top: 10px;
}
.search-input-wrap {
  display: flex;
  gap: 8px;
}
.search-input-wrap input {
  flex: 1;
  padding: 14px 16px;
  border: 2px solid var(--brand-strong);
  border-radius: 12px;
  font-size: 18px;
}
.search-submit {
  padding: 10px 12px;
  border-radius: 10px;
  border: 2px solid var(--brand-strong);
  background: var(--brand-yellow-soft, #FFF8E1);
  cursor: pointer;
}

/* Simple fade transition */
.fade-enter-active, .fade-leave-active { transition: opacity .2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
