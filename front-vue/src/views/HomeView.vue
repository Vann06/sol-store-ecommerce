<template>
    <div>
      <!-- Componente de notificación reutilizable -->
      <MessageNotification 
        :message="message" 
        :type="messageType"
        :isVisible="isVisible"
        @hide="hideMessage" 
      />
      
      <header>
        <Header />
      </header>
  
      <main>
        <section class ="hero-header">
          <img src="/img/logo.png" alt="Logo" class="hero-logo" />
        </section>
        <Information/>
        <HeroSlider />
        <SobreNosotros/>

        <FeaturedCategories />
        <FeaturedThemes />
        <FeaturedProducts />
        
        
        <WhatsappButton />
      </main>
  
    </div>
</template>
  
<script setup>
import { computed } from 'vue'
import Header from '@/components/Header.vue'
import Footer from '@/components/Footer.vue'
import HeroSlider from '@/components/Home/HeroSlider.vue'
import FeaturedCategories from '@/components/Home/FeaturedCategories.vue'
import FeaturedThemes from '@/components/Home/FeaturedThemes.vue'
import FeaturedProducts from '@/components/Home/FeaturedProducts.vue'
import WhatsappButton from '@/components/icons/WhatsappButton.vue'
import Information from '@/components/Home/Information.vue'
import SobreNosotros from '@/components/Home/sobreNosotros.vue'
import MessageNotification from '@/components/MessageNotification.vue'
import CartDebug from '@/components/CartDebug.vue'
import { useMessages } from '@/composables/useMessages.js'

const { message, messageType, isVisible, hideMessage } = useMessages()

</script>

<style scoped>
main {
  display: flex;
  flex-direction: column;
  gap: 40px;
}
.hero-header {
  background-image: url('/img/header-bg.png');
  background-size: cover;
  background-position: center;
  width: 100%;
  height: 500px; 
  display: flex;
  align-items: center;
  justify-content: center;
  padding-top: 150px;
}

.hero-logo {
  width: 60%;
  max-width: 1000px;
  height: auto;
}

/* Estilos para las notificaciones */
.message-notification {
  position: fixed;
  top: 80px;
  right: 20px;
  padding: 15px 20px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  display: flex;
  align-items: center;
  gap: 10px;
  animation: slideIn 0.3s ease-out;
}

.message-success {
  background-color: #e8f5e8;
  color: #7d1c2b;
  border: 1px solid #a27345;
}

.message-error {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.message-warning {
  background-color: #fef3c7;
  color: #a27345;
  border: 1px solid #e5bf60;
}

.message-info {
  background-color: #fdf8f0;
  color: #7d1c2b;
  border: 1px solid #e5bf60;
}

.close-btn {
  background: none;
  border: none;
  font-size: 18px;
  cursor: pointer;
  padding: 0;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}
  
</style>
