<template>
    <section class="login-box">
      <img src="/img/logo.png" alt="Logo" class="logo" />
      <h2 class="admin-title">Login</h2>
  
      <form @submit.prevent="handleLogin">
        <label>Email</label>
        <input type="email" v-model="email" required />
  
        <label>Password</label>
        <input type="password" v-model="password" required />
  
        <button type="submit" class="btn-primary">Login</button>
      </form>
    </section>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import { useRouter } from 'vue-router'
  
  const email = ref('')
  const password = ref('')
  const router = useRouter()
  
  const handleLogin = () => {
    if (email.value === 'admin@example.com' && password.value === 'admin') {
      alert('Logged in!')
      router.push('/')
    } else {
      alert('Invalid credentials')
    }
  }
  </script>
  
  <style scoped>
  .login-box {
    flex: 1;
    padding: 60px 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
  }
  
  .logo {
    height: 60px;
    margin-bottom: 10px;
  }
  
  .admin-title {
    color: #8b0000;
    margin-bottom: 20px;
    font-size: 2rem;
  }
  
  form {
    display: flex;
    flex-direction: column;
    width: 100%;
    max-width: 300px;
  }
  
  label {
    margin-top: 10px;
    font-weight: bold;
    font-size: 14px;
  }
  
  input {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
  }
  
  .btn-primary {
    margin-top: 20px;
    padding: 12px;
    background-color: #8b0000;
    color: white;
    border: none;
    border-radius: 25px;
    font-weight: bold;
    cursor: pointer;
  }
  
  .btn-primary:hover {
    background-color: #a70026;
  }
  </style>
  