<template>
  <header class="header">
    <div class="header__container">
      <!-- LOGO -->
      <div class="header__logo">
        <RouterLink to="/">
          <img src="/img/logo.png" alt="Logo" />
        </RouterLink>
      </div>

      <!-- Botón hamburguesa -->
      <button class="hamburger" @click="toggleMenu">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </button>

      <!-- Contenedor combinado -->
      <div class="header__content" :class="{ open: isMenuOpen }">
        <nav class="header__nav">
          <ul>
            <li><RouterLink to="/">Home</RouterLink></li>
            <li><RouterLink to="/catalogo">Catálogo</RouterLink></li>
            <li><RouterLink to="/categories">Categories ▾</RouterLink></li>
            <li><RouterLink to="/about">About</RouterLink></li>
            <li><RouterLink to="/contact">Contact</RouterLink></li>
            <li><RouterLink to="/faq">FAQ</RouterLink></li>
          </ul>
        </nav>

  <div class="header__tools">
          <div class="search-box">
            <input
              type="text"
              v-model="searchText"
              @keyup.enter="handleSearch"
              placeholder="Search products"
            />
            <img src="/img/search-icon.svg" alt="Search" class="icon-img" @click="handleSearch"/>
          </div>

          <CartWidget />

          <!-- SI ESTÁ LOGUEADO -->
          <div v-if="isLoggedIn" class="user-logged">
            <RouterLink to="/account/orders" class="icon-button">
              <img src="/img/user-icon.svg" alt="User" class="icon-img" />
              <span class="username">{{ user.first_name }}</span>
            </RouterLink>
            <button @click="logout" class="icon-button logout-btn" title="Logout">
              <i class="fa fa-sign-out-alt"></i>
            </button>
          </div>

          <!-- SI NO ESTÁ LOGUEADO -->
          <RouterLink v-else to="/account/login" class="icon-button">
            <img src="/img/user-icon.svg" alt="User" class="icon-img" />
          </RouterLink>

        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
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

const searchText = ref('')
const router = useRouter()

function handleSearch() {
  if (searchText.value.trim() !== ''){
    router.push({ name: 'search', query: { q: searchText.value.trim() } })
  }
}
</script>

<style scoped>
.header {
  background-color: #fff;
  border-top: 5px solid #8B0000;
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
  margin: 0;
  padding: 0;
  gap: 20px;
}

.header__nav ul li {
  padding: 10px 16px;
  border-radius: 6px;
  transition: background-color 0.3s;
  cursor: pointer;
}

.header__nav ul li:hover {
  background-color: #8B0000;
}

.header__nav ul li:hover a {
  color: white;
}

.header__nav a {
  text-decoration: none;
  color: #333;
  font-weight: 500;
  font-size: 15px;
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
  color: #7d1c2b;
  font-size: 16px;
  cursor: pointer;
  padding: 5px;
  border-radius: 4px;
  transition: all 0.3s ease;
}

.logout-btn:hover {
  background-color: #7d1c2b;
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

  .header__content {
    display: none;
    flex-direction: column;
    width: 100%;
    margin-top: 10px;
    gap: 20px;
  }

  .header__content.open {
    display: flex;
  }

  .header__nav ul {
    flex-direction: column;
    gap: 10px;
  }

  .header__tools {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }

  .search-box input {
    width: 100%;
  }
}
</style>
