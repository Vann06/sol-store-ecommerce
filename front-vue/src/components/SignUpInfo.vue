<template>
    <section class="signup-info">
      <h2>¿No tienes una cuenta?</h2>
      <p>Crea una cuenta y disfruta de los siguientes beneficios:</p>
      <ul>
        <li><i class="fa fa-database"></i> Centraliza info de tus productos</li>
        <li><i class="fa fa-bolt"></i> Acelerar el Checkout</li>
        <li><i class="fa fa-star"></i> Obten descuentos exclusivos</li>
        <li><i class="fa fa-truck"></i> Seguimiento de pedidos y devoluciones</li>
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
  