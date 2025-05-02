<template>
    <div>
      <header>
        <Header />
      </header>
  
      <main>
        <section class ="hero-header">
          <img src="/img/logo.png" alt="Logo" class="hero-logo" />
        </section>
        <Information/>
        <HeroSlider />
        <FeaturedCategories />
        <FeaturedThemes />
        <FeaturedProducts />
        <WhatsappButton />
      </main>
  
    </div>
</template>
  
<script setup>
import Header from '@/components/Header.vue'
import Footer from '@/components/Footer.vue'
import HeroSlider from '@/components/Home/HeroSlider.vue'
import FeaturedCategories from '@/components/Home/FeaturedCategories.vue'
import FeaturedThemes from '@/components/Home/FeaturedThemes.vue'
import FeaturedProducts from '@/components/Home/FeaturedProducts.vue'
import WhatsappButton from '@/components/icons/WhatsappButton.vue'
import Information from '@/components/Home/Information.vue'
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

  
</style>
