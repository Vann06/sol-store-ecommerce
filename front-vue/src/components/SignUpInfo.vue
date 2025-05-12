<template>
    <section class="signup-info">
      <h2>¿No tienes una cuenta?</h2>
      <p>Crea una cuenta y disfruta de los siguientes beneficios:</p>
      <ul>
        <li><font-awesome-icon icon="database" /> Centraliza info de tus productos</li>
      </ul>
      <RouterLink to="/account/create">
        <button class="btn-secondary">Crea una cuenta</button>
      </RouterLink>
    </section>
  </template>
  
  <style scoped>
  .signup-info {
    flex: 1;
    background-color: #f5eadc;
    padding: 60px 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }
  
  .signup-info h2 {
    color: #8b2c00;
    font-size: 2rem;
    margin-bottom: 1rem;
  }
  
  .signup-info ul {
    list-style: none;
    padding: 0;
    margin: 20px 0;
  }
  
  .signup-info li {
    margin-bottom: 10px;
    font-size: 15px;
  }
  
  .btn-secondary {
    margin-top: 20px;
    background-color: #c9873ceb;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
  }
  
  .btn-secondary:hover {
    background-color: #63431b;
  }
  </style>
  