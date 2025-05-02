<template>
    <section class="sobre-nosotros">
      <div class="content">
        <img src="/img/logo_2.png" alt="Logo Sol Store" class="logo" />
        <div class="text">
          <h2>Sobre nosotros</h2>
          <p>
            En Sol Store llevamos la pasión por el anime, los cómics y la cultura geek a otro nivel con impresión 3D de alta calidad.
            Desde réplicas épicas hasta props para cosplay, cada pieza está diseñada con detalle y precisión.
          </p>
        </div>
      </div>
    </section>
  </template>
  
  <script setup>
  // No necesitas lógica por ahora
  </script>
  
  <style scoped>
  .sobre-nosotros {
    background-color: #fff;
    padding: 60px 20px;
    display: flex;
    justify-content: center;
  }
  
  .content {
    display: flex;
    align-items: center;
    gap: 40px;
    max-width: 1000px;
    flex-wrap: wrap;
    text-align: left;
  }
  
  .logo {
    width: 150px;
    height: auto;
  }
  
  .text h2 {
    color: #8b0000;
    font-size: 24px;
    margin-bottom: 10px;
    font-weight: bold;
  }
  
  .text p {
    color: #4a4a4a;
    font-size: 16px;
    max-width: 500px;
    line-height: 1.6;
  }
  </style>
  