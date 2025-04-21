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
        <FeaturedBenefits />
        <FeaturedBest />
        <FeaturedSpecial />
        <FeaturedDio />
      </main>
  
    </div>
  </template>
  
  <script setup>
  import Header from '@/components/Header.vue'
  import FeaturedBenefits from '@/components/Home/FeaturedBenefits.vue'
  import HeroSlider from '@/components/Home/HeroSlider.vue'
  import FeaturedCategories from '@/components/Home/FeaturedCategories.vue'
  import FeaturedThemes from '@/components/Home/FeaturedThemes.vue'
  import FeaturedProducts from '@/components/Home/FeaturedProducts.vue'
  import FeaturedBest from '@/components/Home/FeaturedBest.vue'
  import FeaturedSpecial from '@/components/Home/FeaturedSpecial.vue'
  import FeaturedDio from '@/components/Home/FeaturedDio.vue'
  </script>
  
  <style scoped>
  main {
    display: flex;
    flex-direction: column;
    gap: 40px;
  }
  </style>
  
