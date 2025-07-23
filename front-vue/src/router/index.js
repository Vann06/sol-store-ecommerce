import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from '@/stores/user'


import HomeView from '../views/HomeView.vue'
import LoginView from '@/views/LoginView.vue'
import SignUpView from '@/views/SignUpView.vue'
import AccountLayout from '@/views/account/AccountLayout.vue'
import OrdersView from '@/views/account/OrdersView.vue'
import AddressView from '@/views/account/AddressView.vue'
import PasswordView from '@/views/account/PasswordView.vue'
import AccountDetailView from '@/views/account/AccountDetailView.vue'
import ProductDetailView from '@/views/ProductDetailView.vue'
import SearchView from '@/views/SearchView.vue'
import CartView from '@/views/CartView.vue'
import AboutView from '@/views/AboutView.vue'


const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path:'/account/login',
    name: 'login',
    component: LoginView
  },
  {
    path:'/account/create',
    name: 'signup',
    component: SignUpView
  },
  {
    path: '/account',
    component: AccountLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: 'orders',
        name: 'orders',
        component: OrdersView
      },
      {
        path: 'address',
        name: 'address',
        component: AddressView
      },
      {
        path: 'password',
        name: 'password',
        component: PasswordView
      },
      {
        path: 'details',
        name: 'accountDetails',
        component: AccountDetailView
      }
    ]
  },
  
  {
    path: '/faq',
    name: 'faq',
    component: () => import('@/views/FaqView.vue') // Ruta FAQ al mismo nivel que search
  },
  {
    path: '/search',
    name: 'search',
    component: SearchView
  } ,
  
  {
    path: '/product/:id',
    name: 'product-detail',
    component: ProductDetailView
  },
  {
    path: '/cart',
    name: 'cart',
    component: CartView
  },
  {
    path: '/about',
    name: 'about',
    component: AboutView
  }
  //MAS RUTAS
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

//Middleware para proteger las rutas 
router.beforeEach((to, from, next) => {
  const store = useUserStore()

  // Si NO esta logeado lo manda a la seccion de login
  if (to.meta.requiresAuth && !store.user){
    return next('/account/login')
  }
  // Si SI esta logeado lo manda a su cuenta personal
  if (to.meta.requiresGuest && store.user){
    return next('/account/orders')
  }

  next()
})


export default router

