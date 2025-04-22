<template>
  <div class="login-page">
    <Header />
    <div class="auth-container">
      <SignUpInfo />
      <LoginForm />
    </div>
    
  </div>

</template>

<script setup>
import Footer from '@/components/Footer.vue';
import Header from '@/components/Header.vue';
import SignUpInfo from '@/components/SignUpInfo.vue'
import LoginForm from '@/components/LoginForm.vue'
</script>

<style scoped>
.login-page {
  min-height: 120vh;
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
  height: 70vh;
  overflow: hidden;
}

@media (max-width: 1024px) {
  .auth-container {
    flex-direction: column;
    height: auto;
  }
}
</style>
