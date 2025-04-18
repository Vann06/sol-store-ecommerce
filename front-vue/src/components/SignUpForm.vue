<template>
    <div class="form-wrapper">
      <img src="/img/logo.png" alt="Logo" class="logo" />
      <h2 class="title">Crea tu Cuenta</h2>
  
      <form @submit.prevent="handleSubmit">
        <div class="form-grid">
          <div class="form-group">
            <label>Primer Nombre</label>
            <input type="text" v-model="firstName" required />
            <p v-if="errors.first_name" class="error-message">{{ errors.first_name[0] }}</p>
          </div>
  
          <div class="form-group">
            <label>Apellido</label>
            <input type="text" v-model="lastName" required />
            <p v-if="errors.last_name" class="error-message">{{ errors.last_name[0] }}</p>
          </div>
  
          <div class="form-group">
            <label>Email</label>
            <input type="email" v-model="email" required />
            <p v-if="errors.email" class="error-message">{{ errors.email[0] }}</p>
          </div>
  
          <div class="form-group">
            <label>Contraseña</label>
            <input type="password" v-model="password" required />
            <p v-if="errors.password" class="error-message">{{ errors.password[0] }}</p>

          </div>
  
          <div class="form-group">
            <label>Confirmar Contraseña</label>
            <input type="password" v-model="confirmPassword" required />
            <p v-if="errors.password_confirmation" class="error-message">{{ errors.password_confirmation[0] }}</p>
          </div>
        </div>
  
        <div class="checkboxes">
          <label><input type="checkbox" /> I agree to the Terms and Conditions</label>
          <label><input type="checkbox" /> I understand the Privacy Policy</label>
          <label><input type="checkbox" /> I want to receive the newsletter</label>
        </div>
  
        <button class="submit-button" type="submit">Crear Cuenta</button>
      </form>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import axios from 'axios'
  
  const firstName = ref('')
  const lastName = ref('')
  const email = ref('')
  const password = ref('')
  const confirmPassword = ref('')
  const errors = ref({})
 
const handleSubmit = async () => {
  errors.value = {}

  try {
    const response = await axios.post('http://localhost:8000/api/register', {
      first_name: firstName.value,
      last_name: lastName.value,
      email: email.value,
      password: password.value,
      password_confirmation: confirmPassword.value,
    });

    alert('Cuenta creada exitosamente ');
    console.log(response.data);

  } catch (error) {
    if (error.response?.data?.errors){
      errors.value = error.response.data.errors
    }
    else{
      alert('Ocurrio un error. Intenta de nuevo')
    }
  }
}
  </script>
  
  <style scoped>
  .form-wrapper {
  width: 100%;
  max-width: 900px;
  padding: 50px 30px;
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  align-items: center;
  box-sizing: border-box;
}

  .logo {
    height: 69px;
    margin-bottom: 10px;
  }
  
  .title {
    font-size: 28px;
    color: #8B0000;
    margin-bottom: 30px;
  }
  
  form {
    width: 100%;
  }
  
  .form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
  }
  
  .form-group {
    display: flex;
    flex-direction: column;
  }
  
  label {
    font-weight: 600;
    margin-bottom: 6px;
    font-size: 14px;
  }
  
  input[type="text"],
  input[type="email"],
  input[type="password"] {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
  }
  
  .checkboxes {
    margin-top: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    font-size: 14px;
  }
  
  .submit-button {
    margin-top: 30px;
    padding: 14px;
    background-color: #8B0000;
    color: white;
    border: none;
    border-radius: 30px;
    font-weight: bold;
    width: 100%;
    max-width: 250px;
    cursor: pointer;
    align-self: center;
  }
  
  .submit-button:hover {
    background-color: #a70026;
  }

  .error-message {
  color: red;
  font-size: 13px;
  margin-top: 4px;
}

  </style>
  