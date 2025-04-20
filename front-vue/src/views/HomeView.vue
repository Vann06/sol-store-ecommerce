<template>
    <div>
      <header>
        <Header />
      </header>
  
      <main>
        <HeroSlider />
        <FeaturedCategories />
        <FeaturedThemes />
        <FeaturedProducts />
      </main>

      <div class="whatsapp-button-container">
        <a
          :href="whatsappLink"
          target="_blank"
          rel="noopener noreferrer"
          class="whatsapp-button"
        >
          <img
            src="@/assets/whatsapp.svg"
            alt="WhatsApp"
            class="whatsapp-icon"
          />
          <span>Cotizar por WhatsApp</span>
        </a>
      </div>
  
      <footer>
        <Footer />
      </footer>
    </div>
</template>
  
<script setup>
import Header from '@/components/Header.vue'
import Footer from '@/components/Footer.vue'
import HeroSlider from '@/components/Home/HeroSlider.vue'
import FeaturedCategories from '@/components/Home/FeaturedCategories.vue'
import FeaturedThemes from '@/components/Home/FeaturedThemes.vue'
import FeaturedProducts from '@/components/Home/FeaturedProducts.vue'
</script>

<script setup>
const redirectToWhatsApp = () => {
  const phone = import.meta.env.VITE_WHATSAPP_PHONE
  const text  = import.meta.env.VITE_WHATSAPP_MESSAGE
  window.open(`https://wa.me/${phone}?text=${text}`, '_blank')
}
</script>


<style scoped>
main {
  display: flex;
  flex-direction: column;
  gap: 40px;
}
</style>

<style scoped>
.home-container {

  position: relative;
}

.whatsapp-button {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 1rem;
  background-color: #25D366;
  color: white;
  border-radius: 0.5rem;
  text-decoration: none;
  font-weight: bold;
  position: fixed;
  bottom: 1.5rem;
  right: 1.5rem;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  z-index: 1000;
}

.whatsapp-icon {
  width: 1.5rem;
  height: 1.5rem;
  margin-right: 0.5rem;
}
</style>

  