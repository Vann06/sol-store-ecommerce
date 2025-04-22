<template>
    <div class="signup-page">
      <Header />
      <div class="auth-container">
        <SignUpForm />
      </div>
    </div>

  </template>
  
  <script setup>
  import Header from '@/components/Header.vue'
  import Footer from '@/components/Footer.vue'
  import SignUpForm from '@/components/SignUpForm.vue'
  </script>
  
  <style scoped>
  .signup-page {
    min-height: 150vh;
    width: 100vw;
    background-color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    margin: 0;
  }
  
  .auth-container {
    display: flex;
    flex-direction: row;
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    width: 100%;
    max-width: 1200px;
    height: 100vh;
    overflow: hidden;
    justify-content: center;
    align-items: center;
  }
  
  @media (max-width: 1024px) {
    .auth-container {
      flex-direction: column;
      height: auto;
    }
  }
  </style>
  